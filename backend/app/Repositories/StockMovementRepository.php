<?php

namespace App\Repositories;

use App\Models\StockMovement;
use App\Models\StoreProduct;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StockMovementRepository implements StockMovementRepositoryInterface
{
    protected $model;
    protected $storeProduct;

    public function __construct(StockMovement $model, StoreProduct $storeProduct)
    {
        $this->model = $model;
        $this->storeProduct = $storeProduct;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->query()
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'name', 'sku', 'low_stock_threshold');
                },
                'store' => function ($query) {
                    $query->select('id', 'name', 'location');
                }
            ]);

        // Use compound index for date range queries
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Use compound index for type-based queries
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Use compound index for store and product queries
        if (isset($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        // Use compound index for sorting
        return $query->orderBy('product_id', 'desc')
            ->orderBy('store_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $movement = $this->model->create($data);

            // Update store product quantity
            $this->updateStoreProductQuantity(
                $data['product_id'],
                $data['store_id'],
                $data['quantity'],
                $data['type']
            );

            return $movement;
        });
    }

    public function getByProduct(int $productId, array $filters = [])
    {
        $query = $this->model->where('product_id', $productId);

        if (isset($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getByStore(int $storeId, array $filters = [])
    {
        $query = $this->model->where('store_id', $storeId);

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getStockLevel(int $productId, ?int $storeId = null)
    {
        $query = $this->storeProduct->where('product_id', $productId);

        if ($storeId) {
            return $query->where('store_id', $storeId)->sum('quantity');
        }

        return $query->sum('quantity');
    }

    public function adjustStock(int $productId, int $storeId, int $quantity, string $reason, ?string $notes = null)
    {
        return DB::transaction(function () use ($productId, $storeId, $quantity, $reason, $notes) {
            // Create stock movement
            $movement = $this->create([
                'product_id' => $productId,
                'store_id' => $storeId,
                'quantity' => $quantity,
                'type' => 'adjustment',
                'reason' => $reason,
                'notes' => $notes
            ]);

            // Check if stock is below threshold after adjustment
            $this->checkLowStockThreshold($productId, $storeId);

            return $movement;
        });
    }

    public function transferStock(int $productId, int $fromStoreId, int $toStoreId, int $quantity)
    {
        return DB::transaction(function () use ($productId, $fromStoreId, $toStoreId, $quantity) {
            // Create outgoing movement
            $this->create([
                'product_id' => $productId,
                'store_id' => $fromStoreId,
                'quantity' => -$quantity,
                'type' => 'transfer_out',
                'reference_id' => $toStoreId
            ]);

            // Create incoming movement
            $this->create([
                'product_id' => $productId,
                'store_id' => $toStoreId,
                'quantity' => $quantity,
                'type' => 'transfer_in',
                'reference_id' => $fromStoreId
            ]);

            // Check stock levels in both stores
            $this->checkLowStockThreshold($productId, $fromStoreId);
            $this->checkLowStockThreshold($productId, $toStoreId);

            return true;
        });
    }

    protected function updateStoreProductQuantity(int $productId, int $storeId, int $quantity, string $type)
    {
        $storeProduct = $this->storeProduct
            ->firstOrCreate(
                ['store_id' => $storeId, 'product_id' => $productId],
                ['quantity' => 0]
            );

        // Determine if we should add or subtract based on movement type
        $multiplier = in_array($type, ['purchase', 'transfer_in', 'return', 'adjustment_add']) ? 1 : -1;
        $storeProduct->increment('quantity', $quantity * $multiplier);

        return $storeProduct;
    }

    protected function checkLowStockThreshold(int $productId, int $storeId)
    {
        $storeProduct = $this->storeProduct
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();

        $product = $storeProduct->product;

        if ($storeProduct->quantity <= $product->stock_alert_threshold) {
            event(new \App\Events\LowStockAlert($product, $storeProduct->store, $storeProduct->quantity));
        }
    }
}