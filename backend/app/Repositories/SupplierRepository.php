<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SupplierRepository implements SupplierRepositoryInterface
{
    protected $model;
    protected $product;

    public function __construct(Supplier $model, Product $product)
    {
        $this->model = $model;
        $this->product = $product;
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
                  ->orWhere('email', 'like', "%{$filters['search']}%")
                  ->orWhere('phone', 'like', "%{$filters['search']}%");
            });
        }

        return $query->withCount(['products', 'purchases'])
            ->orderBy('name')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id)
    {
        return $this->model->withCount(['products', 'purchases'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $supplier = $this->model->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'tax_number' => $data['tax_number'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'notes' => $data['notes'] ?? null
            ]);

            if (isset($data['products'])) {
                $this->product->whereIn('id', $data['products'])
                    ->update(['supplier_id' => $supplier->id]);
            }

            return $supplier;
        });
    }

    public function update(int $id, array $data)
    {
        $supplier = $this->findById($id);

        return DB::transaction(function () use ($supplier, $data) {
            $supplier->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? $supplier->address,
                'tax_number' => $data['tax_number'] ?? $supplier->tax_number,
                'is_active' => $data['is_active'] ?? $supplier->is_active,
                'notes' => $data['notes'] ?? $supplier->notes
            ]);

            if (isset($data['products'])) {
                // Reset old products
                $this->product->where('supplier_id', $supplier->id)
                    ->update(['supplier_id' => null]);

                // Set new products
                $this->product->whereIn('id', $data['products'])
                    ->update(['supplier_id' => $supplier->id]);
            }

            return $supplier->fresh();
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $supplier = $this->findById($id);
            
            // Remove supplier_id from products
            $this->product->where('supplier_id', $id)
                ->update(['supplier_id' => null]);

            return $supplier->delete();
        });
    }

    public function getSupplierProducts(int $supplierId, array $filters = [])
    {
        $query = $this->product->where('supplier_id', $supplierId);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('sku', 'like', "%{$filters['search']}%")
                  ->orWhere('barcode', 'like', "%{$filters['search']}%");
            });
        }

        return $query->with('category')
            ->orderBy('name')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getPurchaseHistory(int $supplierId, array $filters = [])
    {
        $supplier = $this->findById($supplierId);

        $query = $supplier->purchases()
            ->with(['items.product', 'store', 'user']);

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        if (isset($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        return $query->orderBy('date', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
}