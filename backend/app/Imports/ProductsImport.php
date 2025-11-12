<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\ProductService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $productService;
    
    public function __construct()
    {
        $this->productService = app(ProductService::class);
    }

    public function model(array $row)
    {
        try {
            // First create or get category and supplier
            $category = Category::firstOrCreate(
                ['name' => $row['category']], 
                ['description' => '', 'is_active' => true]
            );
            
            $supplier = Supplier::firstOrCreate(
                ['name' => $row['supplier']], 
                [
                    'email' => '',
                    'phone' => '',
                    'is_active' => true,
                ]
            );

            // Then create the product using our service
            return $this->productService->createProduct([
                'name' => $row['name'],
                'sku' => $row['sku'] ?? Str::random(10),
                'barcode' => $row['barcode'] ?? null,
                'description' => $row['description'] ?? '',
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'purchase_price' => $row['unit_price'],
                'selling_price' => $row['selling_price'],
                'quantity' => $row['initial_quantity'] ?? 0,
                'low_stock_threshold' => $row['stock_alert_threshold'] ?? 10,
                'tax_rate' => $row['tax_rate'] ?? 0,
                'unit' => $row['unit'] ?? 'piece',
                'is_active' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing product: ' . $e->getMessage(), [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'supplier' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'initial_quantity' => ['nullable', 'integer', 'min:0'],
            'stock_alert_threshold' => ['nullable', 'integer', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'unit' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The product name is required',
            'category.required' => 'The category name is required',
            'supplier.required' => 'The supplier name is required',
            'sku.unique' => 'This SKU is already in use',
            'barcode.unique' => 'This barcode is already in use',
            'unit_price.required' => 'The purchase price is required',
            'unit_price.min' => 'The purchase price must be greater than or equal to 0',
            'selling_price.required' => 'The selling price is required',
            'selling_price.min' => 'The selling price must be greater than or equal to 0',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}