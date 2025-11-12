<?php

namespace App\Services;

use App\Events\LowStockAlert;
use App\Models\StockReservation;
use App\Models\StockTransfer;
use App\Models\Store;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StockMovementService
{
    protected $stockMovementRepository;
    protected $productRepository;

    public function __construct(
        StockMovementRepositoryInterface $stockMovementRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->stockMovementRepository = $stockMovementRepository;
        $this->productRepository = $productRepository;
    }

    public function getAllMovements(array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->stockMovementRepository->getAll($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching stock movements', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getProductMovements(int $productId, array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->stockMovementRepository->getByProduct($productId, $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching product stock movements', [
                'product_id' => $productId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getStoreMovements(int $storeId, array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->stockMovementRepository->getByStore($storeId, $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching store stock movements', [
                'store_id' => $storeId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function adjustStock(array $data): array
    {
        try {
            // Validate stock level
            $currentStock = $this->stockMovementRepository->getStockLevel(
                $data['product_id'],
                $data['store_id']
            );

            // For negative adjustments, ensure we have enough stock
            if ($data['quantity'] < 0 && abs($data['quantity']) > $currentStock) {
                throw new \Exception('Insufficient stock for adjustment');
            }

            // Create the movement
            $movement = $this->stockMovementRepository->adjustStock(
                $data['product_id'],
                $data['store_id'],
                $data['quantity'],
                $data['reason'],
                $data['notes'] ?? null
            );

            // Get updated stock level
            $newStock = $this->stockMovementRepository->getStockLevel(
                $data['product_id'],
                $data['store_id']
            );

            return [
                'movement' => $movement,
                'previous_stock' => $currentStock,
                'new_stock' => $newStock
            ];
        } catch (\Exception $e) {
            Log::error('Error adjusting stock', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function transferStock(array $data): array
    {
        try {
            // Create new stock transfer record
            $transfer = StockTransfer::create([
                'from_store_id' => $data['from_store_id'],
                'to_store_id' => $data['to_store_id'],
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'status' => Store::TRANSFER_STATUS_PENDING,
                'reference_number' => StockTransfer::generateReferenceNumber(),
                'initiated_by' => Auth::id(),
                'notes' => $data['notes'] ?? null,
            ]);

            // Create stock reservation in source store
            $reservation = StockReservation::create([
                'product_id' => $data['product_id'],
                'store_id' => $data['from_store_id'],
                'quantity' => $data['quantity'],
                'reference_type' => StockTransfer::class,
                'reference_id' => $transfer->id,
                'status' => StockReservation::STATUS_ACTIVE,
                'expires_at' => now()->addMinutes(30),  // 30 min reservation
            ]);

            // Validate source stock level (including reservations)
            $sourceStock = $this->stockMovementRepository->getStockLevel(
                $data['product_id'],
                $data['from_store_id']
            );

            $reservedStock = StockReservation::where('product_id', $data['product_id'])
                ->where('store_id', $data['from_store_id'])
                ->where('status', StockReservation::STATUS_ACTIVE)
                ->sum('quantity');

            $availableStock = $sourceStock - $reservedStock;

            if ($data['quantity'] > $availableStock) {
                $transfer->cancel();
                throw new \Exception('Insufficient available stock for transfer');
            }

            // Attach reservation to transfer
            $transfer->reservation()->save($reservation);

            // Complete the transfer
            $transfer->complete(Auth::user());

            // Get updated stock levels
            $newSourceStock = $this->stockMovementRepository->getStockLevel(
                $data['product_id'],
                $data['from_store_id']
            );

            $newDestinationStock = $this->stockMovementRepository->getStockLevel(
                $data['product_id'],
                $data['to_store_id']
            );

            return [
                'product_id' => $data['product_id'],
                'from_store' => [
                    'id' => $data['from_store_id'],
                    'previous_stock' => $sourceStock,
                    'new_stock' => $newSourceStock
                ],
                'to_store' => [
                    'id' => $data['to_store_id'],
                    'previous_stock' => $newDestinationStock - $data['quantity'],
                    'new_stock' => $newDestinationStock
                ],
                'quantity' => $data['quantity']
            ];
        } catch (\Exception $e) {
            Log::error('Error transferring stock', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function checkLowStock(int $productId, ?int $storeId = null): bool
    {
        try {
            $product = $this->productRepository->findById($productId);
            $currentStock = $this->stockMovementRepository->getStockLevel($productId, $storeId);

            if ($currentStock <= $product->stock_alert_threshold) {
                // If checking a specific store
                if ($storeId) {
                    event(new LowStockAlert($product, $storeId, $currentStock));
                    return true;
                }

                // For global stock level
                if ($product->global_stock_alert && $currentStock <= $product->global_stock_threshold) {
                    event(new LowStockAlert($product, null, $currentStock));
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error checking low stock', [
                'product_id' => $productId,
                'store_id' => $storeId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getStockSummary(array $filters = []): Collection
    {
        try {
            $products = $this->productRepository->all();
            $summary = collect();

            foreach ($products as $product) {
                $globalStock = $this->stockMovementRepository->getStockLevel($product->id);
                $storeStocks = collect();

                // Get stock per store if stores are provided
                if (isset($filters['store_ids'])) {
                    foreach ($filters['store_ids'] as $storeId) {
                        $storeStock = $this->stockMovementRepository->getStockLevel($product->id, $storeId);
                        $storeStocks->put($storeId, $storeStock);
                    }
                }

                $summary->push([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name,
                    'global_stock' => $globalStock,
                    'store_stocks' => $storeStocks,
                    'low_stock' => $globalStock <= $product->stock_alert_threshold,
                    'stock_value' => $globalStock * $product->purchase_price
                ]);
            }

            return $summary;
        } catch (\Exception $e) {
            Log::error('Error generating stock summary', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}