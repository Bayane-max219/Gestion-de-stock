<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(): Collection
    {
        return $this->productRepository->all();
    }

    public function getPaginatedProducts(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->paginate($filters, $perPage);
    }

    public function createProduct(array $data): Product
    {
        // Validate unique SKU and barcode
        $this->validateUniqueIdentifiers($data);

        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadProductImage($data['image']);
        }

        return $this->productRepository->create($data);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        // Validate unique SKU and barcode
        $this->validateUniqueIdentifiers($data, $product->id);

        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $this->uploadProductImage($data['image']);
        }

        $this->productRepository->update($product, $data);
        return $product->fresh();
    }

    public function deleteProduct(Product $product): bool
    {
        // Delete product image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return $this->productRepository->delete($product);
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function getLowStockProducts(): Collection
    {
        return $this->productRepository->getLowStockProducts();
    }

    public function searchProducts(string $query, array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->searchProducts($query, $filters);
    }

    public function updateStock(Product $product, int $quantity, string $type = 'add'): bool
    {
        try {
            return $this->productRepository->updateStock($product, $quantity, $type);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'quantity' => [$e->getMessage()]
            ]);
        }
    }

    protected function validateUniqueIdentifiers(array $data, ?int $excludeId = null): void
    {
        if (isset($data['sku'])) {
            $existingProduct = $this->productRepository->findBySku($data['sku']);
            if ($existingProduct && (!$excludeId || $existingProduct->id !== $excludeId)) {
                throw ValidationException::withMessages([
                    'sku' => ['This SKU is already in use.']
                ]);
            }
        }

        if (isset($data['barcode'])) {
            $existingProduct = $this->productRepository->findByBarcode($data['barcode']);
            if ($existingProduct && (!$excludeId || $existingProduct->id !== $excludeId)) {
                throw ValidationException::withMessages([
                    'barcode' => ['This barcode is already in use.']
                ]);
            }
        }
    }

    protected function uploadProductImage(UploadedFile $image): string
    {
        return $image->store('product-images', 'public');
    }

    public function checkLowStockAndNotify(): void
    {
        $lowStockProducts = $this->getLowStockProducts();
        
        foreach ($lowStockProducts as $product) {
            // Only notify if not already notified recently
            if (!$product->last_low_stock_notification_at || 
                $product->last_low_stock_notification_at->addDays(7)->isPast()) {
                
                event(new \App\Events\LowStockAlert($product));
                
                $product->update([
                    'last_low_stock_notification_at' => now()
                ]);
            }
        }
    }
}