<?php

namespace App\Repositories;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Repositories\Interfaces\PurchaseRepositoryInterface;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    protected $model;
    protected $purchaseItem;
    protected $stockMovementRepository;

    public function __construct(
        Purchase $model,
        PurchaseItem $purchaseItem,
        StockMovementRepositoryInterface $stockMovementRepository
    ) {
        $this->model = $model;
        $this->purchaseItem = $purchaseItem;
        $this->stockMovementRepository = $stockMovementRepository;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->query()
            ->with(['items.product', 'supplier', 'store', 'user']);

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        if (isset($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id)
    {
        return $this->model->with(['items.product', 'supplier', 'store', 'user'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate receipt number if not provided
            if (!isset($data['receipt_number'])) {
                $data['receipt_number'] = $this->generateReceiptNumber();
            }

            // Create purchase
            $purchase = $this->model->create([
                'receipt_number' => $data['receipt_number'],
                'date' => $data['date'] ?? now(),
                'supplier_id' => $data['supplier_id'],
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

            // Create purchase items and update stock
            foreach ($data['items'] as $item) {
                $this->purchaseItem->create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal']
                ]);

                // Increase stock
                $this->stockMovementRepository->create([
                    'product_id' => $item['product_id'],
                    'store_id' => $purchase->store_id,
                    'quantity' => $item['quantity'],
                    'type' => 'purchase',
                    'reference_id' => $purchase->id
                ]);
            }

            return $purchase;
        });
    }

    public function update(int $id, array $data)
    {
        $purchase = $this->findById($id);
        
        return DB::transaction(function () use ($purchase, $data) {
            // Update main purchase data
            $purchase->update([
                'payment_status' => $data['payment_status'] ?? $purchase->payment_status,
                'paid_amount' => $data['paid_amount'] ?? $purchase->paid_amount,
                'notes' => $data['notes'] ?? $purchase->notes,
                'status' => $data['status'] ?? $purchase->status
            ]);

            return $purchase;
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $purchase = $this->findById($id);
            
            // Reverse stock movements
            foreach ($purchase->items as $item) {
                $this->stockMovementRepository->create([
                    'product_id' => $item->product_id,
                    'store_id' => $purchase->store_id,
                    'quantity' => -$item->quantity,
                    'type' => 'purchase_return',
                    'reference_id' => $purchase->id
                ]);
            }

            return $purchase->delete();
        });
    }

    public function generateReceiptNumber()
    {
        $lastPurchase = $this->model->orderBy('id', 'desc')->first();
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        if (!$lastPurchase) {
            return "PO-{$year}{$month}0001";
        }

        $lastNumber = intval(substr($lastPurchase->receipt_number, -4));
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return "PO-{$year}{$month}{$newNumber}";
    }

    public function getBySupplier(int $supplierId, array $filters = [])
    {
        $query = $this->model->where('supplier_id', $supplierId);

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        return $query->with(['items.product', 'store', 'user'])
            ->orderBy('date', 'desc')
            ->paginate($filters['per_page'] ?? 15);
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

        return $query->with(['items.product', 'supplier', 'user'])
            ->orderBy('date', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
}