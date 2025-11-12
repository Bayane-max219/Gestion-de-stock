<?php

namespace App\Services;

use App\Exports\SuppliersExport;
use App\Imports\SuppliersImport;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SupplierService
{
    protected $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->supplierRepository->getAll($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching suppliers', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function findById(int $id): array
    {
        try {
            $supplier = $this->supplierRepository->findById($id);
            return [
                'supplier' => $supplier,
                'products_count' => $supplier->products_count,
                'purchases_count' => $supplier->purchases_count
            ];
        } catch (\Exception $e) {
            Log::error('Error finding supplier', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function create(array $data): array
    {
        try {
            $supplier = $this->supplierRepository->create($data);
            return [
                'supplier' => $supplier,
                'message' => 'Supplier created successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Error creating supplier', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $supplier = $this->supplierRepository->update($id, $data);
            return [
                'supplier' => $supplier,
                'message' => 'Supplier updated successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Error updating supplier', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function delete(int $id): array
    {
        try {
            $this->supplierRepository->delete($id);
            return ['message' => 'Supplier deleted successfully'];
        } catch (\Exception $e) {
            Log::error('Error deleting supplier', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getProducts(int $supplierId, array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->supplierRepository->getSupplierProducts($supplierId, $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching supplier products', [
                'supplier_id' => $supplierId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getPurchaseHistory(int $supplierId, array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->supplierRepository->getPurchaseHistory($supplierId, $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching supplier purchase history', [
                'supplier_id' => $supplierId,
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function importSuppliers(UploadedFile $file): array
    {
        try {
            $import = new SuppliersImport();
            Excel::import($import, $file);

            return [
                'message' => 'Suppliers imported successfully',
                'imported_count' => $import->getRowCount(),
                'errors' => $import->getErrors()
            ];
        } catch (\Exception $e) {
            Log::error('Error importing suppliers', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function exportSuppliers(array $filters = []): string
    {
        try {
            $filename = 'suppliers_' . now()->format('Y-m-d_His') . '.xlsx';
            Excel::store(new SuppliersExport($filters), $filename, 'exports');

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error exporting suppliers', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getStats(int $supplierId): array
    {
        try {
            $supplier = $this->supplierRepository->findById($supplierId);
            $purchases = $supplier->purchases()
                ->with('items')
                ->get();

            return [
                'total_purchases' => $purchases->count(),
                'total_amount' => $purchases->sum('total'),
                'average_order_value' => $purchases->avg('total') ?? 0,
                'products_count' => $supplier->products()->count(),
                'last_purchase' => $purchases->sortByDesc('date')->first(),
                'purchase_items_count' => $purchases->sum(function ($purchase) {
                    return $purchase->items->count();
                }),
                'on_time_delivery_rate' => $this->calculateOnTimeDeliveryRate($purchases),
                'return_rate' => $this->calculateReturnRate($purchases)
            ];
        } catch (\Exception $e) {
            Log::error('Error getting supplier stats', [
                'supplier_id' => $supplierId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function calculateOnTimeDeliveryRate(Collection $purchases): float
    {
        if ($purchases->isEmpty()) {
            return 0;
        }

        $onTimeDeliveries = $purchases->filter(function ($purchase) {
            return $purchase->delivery_date && 
                   $purchase->expected_date && 
                   $purchase->delivery_date <= $purchase->expected_date;
        })->count();

        return ($onTimeDeliveries / $purchases->count()) * 100;
    }

    protected function calculateReturnRate(Collection $purchases): float
    {
        if ($purchases->isEmpty()) {
            return 0;
        }

        $returnedItems = $purchases->sum(function ($purchase) {
            return $purchase->items->where('returned_quantity', '>', 0)->count();
        });

        $totalItems = $purchases->sum(function ($purchase) {
            return $purchase->items->count();
        });

        return $totalItems > 0 ? ($returnedItems / $totalItems) * 100 : 0;
    }
}