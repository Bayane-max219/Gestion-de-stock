<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Store;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceService
{
    protected $dompdf;
    protected $sale;
    protected $store;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
        $this->store = $sale->store;
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $this->dompdf = new Dompdf($options);
    }

    public function generate(): string
    {
        // Generate unique invoice number for the store
        $invoiceNumber = $this->generateInvoiceNumber();
        
        // Generate HTML content
        $html = view('pdf.invoice', [
            'sale' => $this->sale,
            'store' => $this->store,
            'invoiceNumber' => $invoiceNumber,
        ])->render();

        // Configure PDF
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4');
        $this->dompdf->render();

        // Generate filename
        $filename = $this->generateFilename($invoiceNumber);
        
        // Save PDF to storage
        Storage::put("invoices/{$filename}", $this->dompdf->output());

        // Update sale with invoice info
        $this->sale->update([
            'invoice_number' => $invoiceNumber,
            'invoice_path' => "invoices/{$filename}"
        ]);

        return "invoices/{$filename}";
    }

    protected function generateInvoiceNumber(): string
    {
        $prefix = strtoupper(Str::substr($this->store->name, 0, 3));
        $year = date('Y');
        $month = date('m');
        
        // Get the last invoice number for this store and month
        $lastInvoice = Sale::where('store_id', $this->store->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('invoice_number')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $sequence = (int) substr($lastInvoice->invoice_number, -4);
            $sequence++;
        } else {
            $sequence = 1;
        }

        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }

    protected function generateFilename(string $invoiceNumber): string
    {
        return sprintf(
            'invoice_%s_%s.pdf',
            $invoiceNumber,
            $this->sale->id
        );
    }
}