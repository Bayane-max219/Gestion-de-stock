<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with(['category', 'supplier'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'SKU',
            'Barcode',
            'Name',
            'Category',
            'Supplier',
            'Unit Price',
            'Selling Price',
            'Stock Alert Threshold',
            'Description',
            'Status',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->sku,
            $product->barcode,
            $product->name,
            $product->category->name ?? '',
            $product->supplier->name ?? '',
            $product->unit_price,
            $product->selling_price,
            $product->stock_alert_threshold,
            $product->description,
            $product->is_active ? 'Active' : 'Inactive',
        ];
    }
}