<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Services\AnalyticsService;

class AnalyticsExport implements FromArray, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $type;
    protected $period;
    protected $year;
    protected $month;
    protected $storeId;
    protected $analyticsService;
    protected $data;

    public function __construct($type, $period, $year, $month, $storeId = null)
    {
        $this->type = $type;
        $this->period = $period;
        $this->year = $year;
        $this->month = $month;
        $this->storeId = $storeId;
        $this->analyticsService = app(AnalyticsService::class);
        $this->loadData();
    }

    protected function loadData()
    {
        switch ($this->type) {
            case 'sales':
                $this->data = $this->analyticsService->getSalesAnalytics(
                    $this->period,
                    $this->year,
                    $this->month,
                    $this->storeId
                );
                break;
            case 'purchases':
                $this->data = $this->analyticsService->getPurchasesAnalytics(
                    $this->period,
                    $this->year,
                    $this->month,
                    $this->storeId
                );
                break;
            case 'stock':
                $this->data = $this->analyticsService->getStockAnalytics($this->storeId);
                break;
            case 'cashflow':
                $this->data = $this->analyticsService->getCashflowAnalytics(
                    $this->period,
                    $this->year,
                    $this->month,
                    $this->storeId
                );
                break;
            case 'top_products':
                $this->data = $this->analyticsService->getTopProducts(10, $this->period, $this->storeId);
                break;
        }
    }

    public function array(): array
    {
        $rows = [];
        $labels = $this->data['labels'];
        $datasets = $this->data['datasets'];

        foreach ($labels as $index => $label) {
            $row = ['date' => $label];
            foreach ($datasets as $dataset) {
                $row[$dataset['label']] = $dataset['data'][$index] ?? 0;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public function headings(): array
    {
        $headings = ['Date/Period'];
        foreach ($this->data['datasets'] as $dataset) {
            $headings[] = $dataset['label'];
        }
        return $headings;
    }

    public function map($row): array
    {
        return array_values($row);
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = chr(65 + count($this->data['datasets']));

        // Style the header
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '4F81BD'],
            ],
        ]);

        // Auto-size columns
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A1:' . $lastColumn . (count($this->array()) + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Align numbers right
        $sheet->getStyle('B2:' . $lastColumn . (count($this->array()) + 1))->getNumberFormat()
            ->setFormatCode('#,##0.00');

        return $sheet;
    }

    public function title(): string
    {
        return ucfirst($this->type) . ' Analytics';
    }
}