<?php

namespace App\Repositories\Interfaces;

interface SupplierRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getSupplierProducts(int $supplierId, array $filters = []);
    public function getPurchaseHistory(int $supplierId, array $filters = []);
}