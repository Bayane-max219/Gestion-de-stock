<?php

namespace App\Repositories\Interfaces;

interface StockMovementRepositoryInterface
{
    public function getAll(array $filters = []);
    public function create(array $data);
    public function getByProduct(int $productId, array $filters = []);
    public function getByStore(int $storeId, array $filters = []);
    public function getStockLevel(int $productId, ?int $storeId = null);
    public function adjustStock(int $productId, int $storeId, int $quantity, string $reason, ?string $notes = null);
    public function transferStock(int $productId, int $fromStoreId, int $toStoreId, int $quantity);
}