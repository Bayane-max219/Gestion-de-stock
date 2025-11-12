<?php

namespace App\Repositories\Interfaces;

interface SaleRepositoryInterface
{
    public function getAll(array $filters = []);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getSalesByDateRange(string $startDate, string $endDate);
    public function generateInvoiceNumber();
    public function getByStore(int $storeId, array $filters = []);
}