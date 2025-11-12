<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchasesExport;
use Barryvdh\DomPDF\Facade\PDF;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user', 'items.product'])
            ->when($request->filled('store_id'), function ($q) use ($request) {
                $q->where('store_id', $request->store_id);
            })
            ->when($request->filled('supplier_id'), function ($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('payment_status'), function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('purchase_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('purchase_date', '<=', $request->date_to);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('purchase_number', 'like', "%{$request->search}%")
                        ->orWhereHas('supplier', function ($q) use ($request) {
                            $q->where('name', 'like', "%{$request->search}%");
                        });
                });
            });

        $purchases = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($purchases);
    }

    public function store(PurchaseRequest $request)
    {
        DB::beginTransaction();

        try {
            $purchase = new Purchase($request->except('items'));
            $purchase->user_id = auth()->id();
            $purchase->generatePurchaseNumber();
            $purchase->save();

            $subtotal = 0;
            $tax = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $purchaseItem = new PurchaseItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                $purchase->items()->save($purchaseItem);
                $subtotal += $purchaseItem->subtotal;
                $tax += $purchaseItem->tax;
            }

            // Update purchase totals
            $purchase->subtotal = $subtotal;
            $purchase->tax = $tax;
            $purchase->total = $subtotal + $tax - ($purchase->discount ?? 0);
            $purchase->save();

            DB::commit();

            return response()->json([
                'message' => 'Purchase created successfully',
                'purchase' => $purchase->load(['items.product', 'supplier', 'user']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating purchase',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['items.product', 'supplier', 'user', 'payments', 'stockMovements']);
        return response()->json($purchase);
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        if (!in_array($purchase->status, [Purchase::STATUS_PENDING, Purchase::STATUS_PARTIALLY_RECEIVED])) {
            return response()->json([
                'message' => 'Can only update pending or partially received purchases',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $purchase->update($request->except('items'));

            if ($request->has('items')) {
                // Delete items that were not received
                foreach ($purchase->items as $item) {
                    if ($item->received_quantity == 0) {
                        $item->delete();
                    }
                }

                foreach ($request->items as $itemData) {
                    $item = $purchase->items()->where('product_id', $itemData['product_id'])->first();

                    if ($item) {
                        if ($item->received_quantity == 0) {
                            $item->update([
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['unit_price'],
                            ]);
                        }
                    } else {
                        $purchase->items()->create([
                            'product_id' => $itemData['product_id'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                        ]);
                    }
                }

                // Recalculate totals
                $subtotal = $purchase->items->sum('subtotal');
                $tax = $purchase->items->sum('tax');
                
                $purchase->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $subtotal + $tax - ($purchase->discount ?? 0),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase updated successfully',
                'purchase' => $purchase->load(['items.product', 'supplier', 'user']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating purchase',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Purchase $purchase)
    {
        if (!in_array($purchase->status, [Purchase::STATUS_PENDING])) {
            return response()->json([
                'message' => 'Can only delete pending purchases',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $purchase->items()->delete();
            $purchase->delete();

            DB::commit();

            return response()->json([
                'message' => 'Purchase deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting purchase',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function receive(Request $request, Purchase $purchase)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_items,id',
            'items.*.received_quantity' => 'required|numeric|min:0',
        ]);

        try {
            $purchase->receiveItems($request->items);

            return response()->json([
                'message' => 'Items received successfully',
                'purchase' => $purchase->load(['items.product', 'supplier', 'user']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error receiving items',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new PurchasesExport($request->all()), 'purchases.xlsx');
    }

    public function printPurchaseOrder(Purchase $purchase)
    {
        $purchase->load(['items.product', 'supplier', 'user', 'store']);
        
        $pdf = PDF::loadView('pdf.purchase-order', [
            'purchase' => $purchase,
            'company' => [
                'name' => config('app.name'),
                'address' => config('app.address'),
                'phone' => config('app.phone'),
                'email' => config('app.email'),
                'tax_number' => config('app.tax_number'),
            ],
        ]);

        return $pdf->download("po-{$purchase->purchase_number}.pdf");
    }

    public function printPurchaseReceipt(Purchase $purchase)
    {
        $purchase->load(['items.product', 'supplier', 'user', 'store', 'payments']);

        $pdf = PDF::loadView('pdf.purchase-receipt', [
            'purchase' => $purchase,
            'company' => [
                'name' => config('app.name'),
                'address' => config('app.address'),
                'phone' => config('app.phone'),
                'email' => config('app.email'),
                'tax_number' => config('app.tax_number'),
            ],
        ]);

        return $pdf->download("purchase-receipt-{$purchase->purchase_number}.pdf");
    }

    public function getLowStock(Request $request)
    {
        $products = Product::with(['supplier'])
            ->whereHas('storeProducts', function ($query) {
                $query->whereRaw('quantity <= minimum_quantity');
            })
            ->when($request->filled('store_id'), function ($query) use ($request) {
                $query->whereHas('storeProducts', function ($q) use ($request) {
                    $q->where('store_id', $request->store_id);
                });
            })
            ->get()
            ->map(function ($product) {
                $storeProducts = $product->storeProducts;
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'supplier' => $product->supplier ? [
                        'id' => $product->supplier->id,
                        'name' => $product->supplier->name,
                    ] : null,
                    'stores' => $storeProducts->map(function ($sp) {
                        return [
                            'store_id' => $sp->store_id,
                            'quantity' => $sp->quantity,
                            'minimum_quantity' => $sp->minimum_quantity,
                            'required_quantity' => $sp->minimum_quantity - $sp->quantity,
                        ];
                    }),
                    'last_purchase_date' => $product->purchases()
                        ->latest('purchase_date')
                        ->first()?->purchase_date,
                    'last_purchase_price' => $product->purchase_price,
                ];
            });

        return response()->json($products);
    }

    public function updateStatus(Request $request, Purchase $purchase)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', [
                Purchase::STATUS_PENDING,
                Purchase::STATUS_RECEIVED,
                Purchase::STATUS_PARTIALLY_RECEIVED,
                Purchase::STATUS_CANCELLED,
            ])],
        ]);

        $purchase->status = $request->status;
        
        if ($request->status === Purchase::STATUS_CANCELLED) {
            // Reverse any received quantities
            foreach ($purchase->items as $item) {
                if ($item->received_quantity > 0) {
                    $item->product->decrementStock(
                        $purchase->store_id,
                        $item->received_quantity,
                        'purchase_cancelled',
                        $purchase
                    );
                    $item->received_quantity = 0;
                    $item->save();
                }
            }
        }

        $purchase->save();

        return response()->json([
            'message' => 'Purchase status updated successfully',
            'purchase' => $purchase->load(['items.product', 'supplier', 'user']),
        ]);
    }
}