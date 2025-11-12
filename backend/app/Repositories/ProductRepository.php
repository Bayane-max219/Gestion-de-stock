<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(): Collection
    {
        return Product::with(['category', 'supplier', 'stores'])
            ->orderBy('name')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with([
            'category',
            'supplier',
            'stores' => function ($query) {
                $query->select('stores.id', 'stores.name', 'store_products.quantity');
            }
        ]);

        // Apply filters
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['low_stock'])) {
            $query->whereRaw('quantity <= low_stock_threshold');
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = $filters['sort_by'] ?? 'name';
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return Product::with([
            'category',
            'supplier',
            'stores' => function ($query) {
                $query->select('stores.id', 'stores.name', 'store_products.quantity');
            },
            'stockMovements' => function ($query) {
                $query->latest()->limit(10);
            }
        ])->find($id);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create($data);
            
            if (isset($data['stores'])) {
                foreach ($data['stores'] as $storeId => $quantity) {
                    $product->stores()->attach($storeId, ['quantity' => $quantity]);
                }
            }
            
            return $product;
        });
    }

    public function update(Product $product, array $data): bool
    {
        return DB::transaction(function () use ($product, $data) {
            $updated = $product->update($data);
            
            if (isset($data['stores'])) {
                $product->stores()->sync($data['stores']);
            }
            
            return $updated;
        });
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            // Delete related store quantities
            $product->stores()->detach();
            
            // Delete the product
            return $product->delete();
        });
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::with(['category', 'supplier', 'stores'])
            ->where('sku', $sku)
            ->first();
    }

    public function findByBarcode(string $barcode): ?Product
    {
        return Product::with(['category', 'supplier', 'stores'])
            ->where('barcode', $barcode)
            ->first();
    }

    public function getLowStockProducts(): Collection
    {
        return Product::with(['category', 'supplier'])
            ->whereRaw('quantity <= low_stock_threshold')
            ->orderBy('quantity')
            ->get();
    }

    public function searchProducts(string $query, array $filters = []): LengthAwarePaginator
    {
        $productsQuery = Product::with(['category', 'supplier'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });

        // Apply additional filters
        if (!empty($filters)) {
            $this->applyFilters($productsQuery, $filters);
        }

        return $productsQuery->orderBy('name')->paginate(15);
    }

    public function getProductsByCategory(int $categoryId): Collection
    {
        return Product::where('category_id', $categoryId)
            ->with(['supplier'])
            ->orderBy('name')
            ->get();
    }

    public function updateStock(Product $product, int $quantity, string $type = 'add'): bool
    {
        return DB::transaction(function () use ($product, $quantity, $type) {
            $newQuantity = $type === 'add' 
                ? $product->quantity + $quantity
                : $product->quantity - $quantity;

            if ($newQuantity < 0) {
                throw new \Exception('Stock cannot be negative');
            }

            return $product->update(['quantity' => $newQuantity]);
        });
    }

    protected function applyFilters($query, array $filters): void
    {
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['in_stock'])) {
            $query->where('quantity', '>', 0);
        }

        if (isset($filters['low_stock'])) {
            $query->whereRaw('quantity <= low_stock_threshold');
        }
    }
}