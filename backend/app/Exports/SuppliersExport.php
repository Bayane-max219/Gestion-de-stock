<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Supplier::query()->with(['products', 'purchases']);

        if (isset($this->filters['is_active'])) {
            $query->where('is_active', $this->filters['is_active']);
        }

        if (isset($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->filters['search']}%")
                  ->orWhere('email', 'like', "%{$this->filters['search']}%")
                  ->orWhere('phone', 'like', "%{$this->filters['search']}%");
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Address',
            'Tax Number',
            'Status',
            'Products Count',
            'Total Purchases',
            'Last Purchase Date',
            'Notes',
            'Created At',
            'Updated At'
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->name,
            $supplier->email,
            $supplier->phone,
            $supplier->address,
            $supplier->tax_number,
            $supplier->is_active ? 'Active' : 'Inactive',
            $supplier->products->count(),
            $supplier->purchases->count(),
            $supplier->purchases->max('date'),
            $supplier->notes,
            $supplier->created_at->format('Y-m-d H:i:s'),
            $supplier->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:M1' => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA']
            ]],
        ];
    }
}