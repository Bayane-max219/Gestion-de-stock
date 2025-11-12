<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Client::query()
            ->when(isset($this->filters['status']), function ($q) {
                $q->where('status', $this->filters['status']);
            })
            ->when(isset($this->filters['city']), function ($q) {
                $q->where('city', 'like', "%{$this->filters['city']}%");
            })
            ->when(isset($this->filters['search']), function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', "%{$this->filters['search']}%")
                        ->orWhere('email', 'like', "%{$this->filters['search']}%")
                        ->orWhere('phone', 'like', "%{$this->filters['search']}%")
                        ->orWhere('code', 'like', "%{$this->filters['search']}%");
                });
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Email',
            'Phone',
            'Address',
            'City',
            'Tax Number',
            'Credit Limit',
            'Payment Terms (days)',
            'Total Sales',
            'Total Paid',
            'Balance Due',
            'Status',
            'Created At',
        ];
    }

    public function map($client): array
    {
        return [
            $client->code,
            $client->name,
            $client->email,
            $client->phone,
            $client->address,
            $client->city,
            $client->tax_number,
            number_format($client->credit_limit, 2),
            $client->payment_terms,
            number_format($client->total_sales, 2),
            number_format($client->total_paid, 2),
            number_format($client->total_due, 2),
            ucfirst($client->status),
            $client->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}