<?php

namespace App\Exports;

use App\Models\CashRegister;
use App\Models\CashTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class CashTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $registerId;
    protected $filters;

    public function __construct($registerId, $filters = [])
    {
        $this->registerId = $registerId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = CashTransaction::where('cash_register_id', $this->registerId)
            ->with(['user', 'reference'])
            ->when(isset($this->filters['type']), function ($q) {
                $q->where('type', $this->filters['type']);
            })
            ->when(isset($this->filters['date_from']), function ($q) {
                $q->whereDate('created_at', '>=', $this->filters['date_from']);
            })
            ->when(isset($this->filters['date_to']), function ($q) {
                $q->whereDate('created_at', '<=', $this->filters['date_to']);
            });

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Date & Time',
            'Type',
            'Amount',
            'Balance After',
            'Description',
            'Payment Method',
            'Reference',
            'Recorded By',
            'Notes'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->created_at->format('Y-m-d H:i:s'),
            ucfirst($transaction->type),
            $transaction->amount,
            $transaction->balance_after,
            $transaction->description,
            ucfirst($transaction->payment_method),
            $transaction->reference ? class_basename($transaction->reference_type) . ' #' . $transaction->reference->id : '-',
            $transaction->user->name,
            $transaction->notes ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $register = CashRegister::findOrFail($this->registerId);

        // Set title
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'Cash Register Transactions Report');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Set register info
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', "Register #" . $register->id . " - " . $register->store->name);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', "Opening Date: " . $register->opening_date->format('Y-m-d H:i:s') . 
            " - Status: " . ucfirst($register->status));
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

        // Style headers
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => 'E2EFDA']
            ]
        ]);

        // Auto size columns
        foreach(range('A','I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }
}