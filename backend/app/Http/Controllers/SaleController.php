<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SaleRequest;
use Barryvdh\DomPDF\Facade\PDF;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['client', 'user', 'items.product'])
            ->when($request->filled('store_id'), function ($q) use ($request) {
                $q->where('store_id', $request->store_id);
            })
            ->when($request->filled('client_id'), function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            })
            ->when($request->filled('payment_status'), function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('sale_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('sale_date', '<=', $request->date_to);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('invoice_number', 'like', "%{$request->search}%")
                        ->orWhereHas('client', function ($q) use ($request) {
                            $q->where('name', 'like', "%{$request->search}%");
                        });
                });
            });

        $sales = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($sales);
    }

    public function store(SaleRequest $request)
    {
        DB::beginTransaction();

        try {
            $sale = new Sale($request->except('items'));
            $sale->user_id = auth()->id();
            $sale->sale_date = now();
            $sale->generateInvoiceNumber();
            $sale->save();

            $subtotal = 0;

            foreach ($request->items as $item) {
                // Check stock availability
                $storeProduct = StoreProduct::where([
                    'store_id' => $sale->store_id,
                    'product_id' => $item['product_id'],
                ])->first();

                if (!$storeProduct || $storeProduct->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product ID: {$item['product_id']}");
                }

                $product = Product::findOrFail($item['product_id']);
                
                $saleItem = new SaleItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? $product->selling_price,
                    'subtotal' => $item['quantity'] * ($item['unit_price'] ?? $product->selling_price),
                ]);

                $sale->items()->save($saleItem);
                $subtotal += $saleItem->subtotal;
            }

            // Update sale totals
            $sale->subtotal = $subtotal;
            $sale->tax = ($subtotal * config('app.tax_rate', 0.20));
            $sale->total = $sale->subtotal + $sale->tax - $sale->discount;
            $sale->save();

            DB::commit();

            return response()->json([
                'message' => 'Sale created successfully',
                'sale' => $sale->load(['items.product', 'client', 'user']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating sale',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'client', 'user', 'payments']);
        return response()->json($sale);
    }

    public function update(SaleRequest $request, Sale $sale)
    {
        if ($sale->payment_status !== Sale::STATUS_PENDING) {
            return response()->json([
                'message' => 'Can only update pending sales',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $sale->update($request->except('items'));

            if ($request->has('items')) {
                // Delete old items and restore stock
                foreach ($sale->items as $item) {
                    $item->delete(); // This will trigger the deleted event to restore stock
                }

                $subtotal = 0;

                // Add new items
                foreach ($request->items as $item) {
                    // Check stock availability
                    $storeProduct = StoreProduct::where([
                        'store_id' => $sale->store_id,
                        'product_id' => $item['product_id'],
                    ])->first();

                    if (!$storeProduct || $storeProduct->quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product ID: {$item['product_id']}");
                    }

                    $product = Product::findOrFail($item['product_id']);
                    
                    $saleItem = new SaleItem([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'] ?? $product->selling_price,
                        'subtotal' => $item['quantity'] * ($item['unit_price'] ?? $product->selling_price),
                    ]);

                    $sale->items()->save($saleItem);
                    $subtotal += $saleItem->subtotal;
                }

                // Update sale totals
                $sale->subtotal = $subtotal;
                $sale->tax = ($subtotal * config('app.tax_rate', 0.20));
                $sale->total = $sale->subtotal + $sale->tax - $sale->discount;
                $sale->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Sale updated successfully',
                'sale' => $sale->load(['items.product', 'client', 'user']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating sale',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Sale $sale)
    {
        if ($sale->payment_status !== Sale::STATUS_PENDING) {
            return response()->json([
                'message' => 'Can only delete pending sales',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Delete items will trigger the deleted event to restore stock
            $sale->items()->delete();
            $sale->delete();

            DB::commit();

            return response()->json([
                'message' => 'Sale deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting sale',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function printInvoice(Sale $sale)
    {
        $sale->load(['items.product', 'client', 'user', 'store']);
        
        $pdf = PDF::loadView('pdf.invoice', [
            'sale' => $sale,
            'company' => [
                'name' => config('app.name'),
                'address' => config('app.address'),
                'phone' => config('app.phone'),
                'email' => config('app.email'),
                'tax_number' => config('app.tax_number'),
            ],
        ]);

        return $pdf->download("invoice-{$sale->invoice_number}.pdf");
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,partially_paid,cancelled',
        ]);

        $sale->payment_status = $request->status;
        $sale->save();

        return response()->json([
            'message' => 'Sale status updated successfully',
            'sale' => $sale->load(['items.product', 'client', 'user']),
        ]);
    }
}