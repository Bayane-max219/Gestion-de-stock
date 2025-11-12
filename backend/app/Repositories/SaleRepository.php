<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleRepository implements SaleRepositoryInterface
{
    protected $model;
    protected $saleItem;
    protected $stockMovementRepository;

    public function __construct(
        Sale $model,
        SaleItem $saleItem,
        StockMovementRepositoryInterface $stockMovementRepository
    ) {
        $this->model = $model;
        $this->saleItem = $saleItem;
        $this->stockMovementRepository = $stockMovementRepository;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->query()
            ->with([
                'items' => function ($query) {
                    $query->select('id', 'sale_id', 'product_id', 'quantity', 'unit_price', 'subtotal');
                },
                'items.product' => function ($query) {
                    $query->select('id', 'name', 'sku', 'price');
                },
                'client' => function ($query) {
                    $query->select('id', 'name', 'email', 'phone');
                },
                'store' => function ($query) {
                    $query->select('id', 'name', 'location');
                },
                'user' => function ($query) {
                    $query->select('id', 'name', 'email');
                }
            ]);

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        if (isset($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Use index
        $query->orderBy('created_at', 'desc')
              ->orderBy('invoice_number', 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id)
    {
        return $this->model->with(['items.product', 'client', 'store', 'user'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate invoice number if not provided
            if (!isset($data['invoice_number'])) {
                $data['invoice_number'] = $this->generateInvoiceNumber();
            }

            // Create sale
            $sale = $this->model->create([
                'invoice_number' => $data['invoice_number'],
                'date' => $data['date'] ?? now(),
                'client_id' => $data['client_id'] ?? null,
                'store_id' => $data['store_id'],
                'user_id' => $data['user_id'],
                'subtotal' => $data['subtotal'],
                'tax' => $data['tax'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'total' => $data['total'],
                'paid_amount' => $data['paid_amount'],
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'],
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'completed'
            ]);

            // Create sale items and update stock
            foreach ($data['items'] as $item) {
                $this->saleItem->create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal']
                ]);

                // Decrease stock
                $this->stockMovementRepository->create([
                    'product_id' => $item['product_id'],
                    'store_id' => $sale->store_id,
                    'quantity' => -$item['quantity'],
                    'type' => 'sale',
                    'reference_id' => $sale->id
                ]);
            }

            return $sale;
        });
    }

    public function update(int $id, array $data)
    {
        $sale = $this->findById($id);
        
        return DB::transaction(function () use ($sale, $data) {
            // Update main sale data
            $sale->update([
                'payment_status' => $data['payment_status'] ?? $sale->payment_status,
                'paid_amount' => $data['paid_amount'] ?? $sale->paid_amount,
                'notes' => $data['notes'] ?? $sale->notes,
                'status' => $data['status'] ?? $sale->status
            ]);

            return $sale;
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $sale = $this->findById($id);
            
            // Reverse stock movements
            foreach ($sale->items as $item) {
                $this->stockMovementRepository->create([
                    'product_id' => $item->product_id,
                    'store_id' => $sale->store_id,
                    'quantity' => $item->quantity,
                    'type' => 'sale_return',
                    'reference_id' => $sale->id
                ]);
            }

            return $sale->delete();
        });
    }

    public function getSalesByDateRange(string $startDate, string $endDate)
    {
        return $this->model
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['items.product', 'client', 'store'])
            ->get();
    }

    public function generateInvoiceNumber()
    {
        $storeId = request()->store_id;
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        // Use index on store_id and invoice_number
        $lastSale = $this->model
            ->where('store_id', $storeId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderByDesc('invoice_number')
            ->first();
        
        if (!$lastSale) {
            return "INV-{$storeId}-{$year}{$month}0001";
        }

        $lastNumber = intval(substr($lastSale->invoice_number, -4));
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return "INV-{$storeId}-{$year}{$month}{$newNumber}";
    }

    public function getByStore(int $storeId, array $filters = [])
    {
        $query = $this->model->where('store_id', $storeId);

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        return $query->with(['items.product', 'client', 'user'])
            ->orderBy('date', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
}