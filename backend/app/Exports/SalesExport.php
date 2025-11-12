<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Sale::query()->with(['items.product', 'client', 'store', 'user']);

        if (isset($this->filters['date_from'])) {
            $query->where('date', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->where('date', '<=', $this->filters['date_to']);
        }

        if (isset($this->filters['store_id'])) {
            $query->where('store_id', $this->filters['store_id']);
        }

        if (isset($this->filters['client_id'])) {
            $query->where('client_id', $this->filters['client_id']);
        }

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['payment_status'])) {
            $query->where('payment_status', $this->filters['payment_status']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Date',
            'Store',
            'Client',
            'Cashier',
            'Items Count',
            'Subtotal',
            'Tax',
            'Total',
            'Payment Method',
            'Payment Status',
            'Status'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->invoice_number,
            $sale->date->format('Y-m-d H:i:s'),
            $sale->store->name,
            $sale->client ? $sale->client->name : 'Walk-in Customer',
            $sale->user->name,
            $sale->items->count(),
            $sale->subtotal,
            $sale->tax,
            $sale->total,
            $sale->payment_method,
            $sale->payment_status,
            $sale->status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:L1' => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA']
            ]],
        ];
    }
}