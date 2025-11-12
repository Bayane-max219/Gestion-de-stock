<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function all(): Collection;
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Product;
    public function create(array $data): Product;
    public function update(Product $product, array $data): bool;
    public function delete(Product $product): bool;
    public function findBySku(string $sku): ?Product;
    public function findByBarcode(string $barcode): ?Product;
    public function getLowStockProducts(): Collection;
    public function searchProducts(string $query, array $filters = []): LengthAwarePaginator;
    public function getProductsByCategory(int $categoryId): Collection;
    public function updateStock(Product $product, int $quantity, string $type = 'add'): bool;
}