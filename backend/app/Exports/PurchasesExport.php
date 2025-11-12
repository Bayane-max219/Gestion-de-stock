<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Purchase::query()
            ->with(['supplier', 'store', 'items.product'])
            ->when(isset($this->filters['store_id']), function ($q) {
                $q->where('store_id', $this->filters['store_id']);
            })
            ->when(isset($this->filters['supplier_id']), function ($q) {
                $q->where('supplier_id', $this->filters['supplier_id']);
            })
            ->when(isset($this->filters['status']), function ($q) {
                $q->where('status', $this->filters['status']);
            })
            ->when(isset($this->filters['payment_status']), function ($q) {
                $q->where('payment_status', $this->filters['payment_status']);
            })
            ->when(isset($this->filters['date_from']), function ($q) {
                $q->whereDate('purchase_date', '>=', $this->filters['date_from']);
            })
            ->when(isset($this->filters['date_to']), function ($q) {
                $q->whereDate('purchase_date', '<=', $this->filters['date_to']);
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Purchase Number',
            'Store',
            'Supplier',
            'Date',
            'Expected Date',
            'Items Count',
            'Total Quantity',
            'Received Quantity',
            'Subtotal',
            'Tax',
            'Discount',
            'Total',
            'Status',
            'Payment Status',
            'Payment Due Date',
            'Created By',
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->purchase_number,
            $purchase->store->name,
            $purchase->supplier->name,
            $purchase->purchase_date->format('Y-m-d'),
            $purchase->expected_date->format('Y-m-d'),
            $purchase->items->count(),
            $purchase->items->sum('quantity'),
            $purchase->items->sum('received_quantity'),
            number_format($purchase->subtotal, 2),
            number_format($purchase->tax, 2),
            number_format($purchase->discount ?? 0, 2),
            number_format($purchase->total, 2),
            ucfirst($purchase->status),
            ucfirst($purchase->payment_status),
            $purchase->payment_due_date->format('Y-m-d'),
            $purchase->user->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'I' => ['numberFormat' => ['formatCode' => '#,##0.00']],
            'J' => ['numberFormat' => ['formatCode' => '#,##0.00']],
            'K' => ['numberFormat' => ['formatCode' => '#,##0.00']],
            'L' => ['numberFormat' => ['formatCode' => '#,##0.00']],
        ];
    }
}