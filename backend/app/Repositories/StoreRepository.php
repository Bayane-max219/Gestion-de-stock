<?php

namespace App\Repositories;

use App\Models\Store;
use App\Models\StoreProduct;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StoreRepository implements StoreRepositoryInterface
{
    protected $model;
    protected $storeProduct;

    public function __construct(Store $model, StoreProduct $storeProduct)
    {
        $this->model = $model;
        $this->storeProduct = $storeProduct;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->model->query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('address', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('name')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $store = $this->model->create($data);

            // If products are provided, assign them to the store
            if (isset($data['products'])) {
                foreach ($data['products'] as $product) {
                    $this->assignProductToStore(
                        $store->id,
                        $product['id'],
                        $product['initial_quantity'] ?? 0
                    );
                }
            }

            return $store;
        });
    }

    public function update(int $id, array $data)
    {
        $store = $this->findById($id);
        $store->update($data);
        return $store;
    }

    public function delete(int $id)
    {
        $store = $this->findById($id);
        return $store->delete();
    }

    public function getStoreProducts(int $storeId, array $filters = [])
    {
        $query = $this->storeProduct
            ->where('store_id', $storeId)
            ->with('product');

        if (isset($filters['low_stock'])) {
            $query->whereHas('product', function ($q) {
                $q->whereColumn('store_products.quantity', '<=', 'products.stock_alert_threshold');
            });
        }

        if (isset($filters['category_id'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        if (isset($filters['search'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('sku', 'like', "%{$filters['search']}%")
                  ->orWhere('barcode', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getUserStores(int $userId)
    {
        return $this->model
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('is_active', true)
            ->get();
    }

    public function assignProductToStore(int $storeId, int $productId, int $quantity = 0)
    {
        return $this->storeProduct->updateOrCreate(
            [
                'store_id' => $storeId,
                'product_id' => $productId
            ],
            ['quantity' => $quantity]
        );
    }
}