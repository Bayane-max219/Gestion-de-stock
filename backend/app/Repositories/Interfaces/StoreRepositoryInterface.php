<?php

namespace App\Repositories\Interfaces;

interface StoreRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getStoreProducts(int $storeId, array $filters = []);
    public function getUserStores(int $userId);
    public function assignProductToStore(int $storeId, int $productId, int $quantity = 0);
}