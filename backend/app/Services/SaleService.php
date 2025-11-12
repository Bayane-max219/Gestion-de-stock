<?php

namespace App\Services;

use App\Exports\SalesExport;
use App\Models\Product;
use App\Models\Sale;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SaleService
{
    protected $saleRepository;
    protected $productRepository;
    protected $stockMovementRepository;

    public function __construct(
        SaleRepositoryInterface $saleRepository,
        ProductRepositoryInterface $productRepository,
        StockMovementRepositoryInterface $stockMovementRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
        $this->stockMovementRepository = $stockMovementRepository;
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->saleRepository->getAll($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching sales', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function findById(int $id): array
    {
        try {
            $sale = $this->saleRepository->findById($id);
            return [
                'sale' => $sale,
                'payment_info' => $this->getPaymentInfo($sale)
            ];
        } catch (\Exception $e) {
            Log::error('Error finding sale', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function create(array $data): array
    {
        try {
            DB::beginTransaction();

            // Validate stock availability
            $this->validateStockAvailability($data['items'], $data['store_id']);

            // Calculate totals
            $totals = $this->calculateTotals($data['items']);
            $data = array_merge($data, $totals);

            // Create the sale
            $sale = $this->saleRepository->create($data);

            // Process payment
            $paymentResult = $this->processPayment($sale, $data);

            DB::commit();

            return [
                'sale' => $sale,
                'payment' => $paymentResult,
                'message' => 'Sale completed successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sale', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            DB::beginTransaction();

            $sale = $this->saleRepository->update($id, $data);

            // If payment status is being updated
            if (isset($data['payment_status'])) {
                $paymentResult = $this->processPayment($sale, $data);
            }

            DB::commit();

            return [
                'sale' => $sale,
                'payment' => $paymentResult ?? null,
                'message' => 'Sale updated successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sale', [
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
            DB::beginTransaction();

            // Delete will handle stock reversal through repository
            $this->saleRepository->delete($id);

            DB::commit();

            return ['message' => 'Sale deleted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting sale', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function generateInvoice(int $id): Response
    {
        try {
            $sale = $this->saleRepository->findById($id);
            $data = [
                'sale' => $sale,
                'payment_info' => $this->getPaymentInfo($sale),
                'company' => $this->getCompanyInfo()
            ];

            $pdf = PDF::loadView('pdf.invoice', $data);

            return $pdf->download("invoice-{$sale->invoice_number}.pdf");
        } catch (\Exception $e) {
            Log::error('Error generating invoice', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function exportSales(array $filters = []): string
    {
        try {
            $filename = 'sales_' . now()->format('Y-m-d_His') . '.xlsx';
            Excel::store(new SalesExport($filters), $filename, 'exports');

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error exporting sales', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function validateStockAvailability(array $items, int $storeId): void
    {
        $errors = [];

        foreach ($items as $item) {
            $currentStock = $this->stockMovementRepository->getStockLevel(
                $item['product_id'],
                $storeId
            );

            if ($currentStock < $item['quantity']) {
                $product = Product::find($item['product_id']);
                $errors[] = "Insufficient stock for product '{$product->name}'. Available: {$currentStock}, Requested: {$item['quantity']}";
            }
        }

        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }
    }

    protected function calculateTotals(array $items): array
    {
        $subtotal = 0;
        $tax = 0;

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $subtotal += $itemSubtotal;
            $tax += $itemSubtotal * ($product->tax_rate / 100);
        }

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax
        ];
    }

    protected function processPayment(Sale $sale, array $data): array
    {
        // Handle different payment methods
        switch ($data['payment_method']) {
            case 'cash':
                return $this->processCashPayment($sale, $data);
            case 'card':
                return $this->processCardPayment($sale, $data);
            case 'mixed':
                return $this->processMixedPayment($sale, $data);
            default:
                throw new \Exception('Invalid payment method');
        }
    }

    protected function processCashPayment(Sale $sale, array $data): array
    {
        $change = $data['paid_amount'] - $sale->total;

        if ($change < 0) {
            throw new \Exception('Insufficient payment amount');
        }

        return [
            'method' => 'cash',
            'amount_paid' => $data['paid_amount'],
            'change' => $change,
            'status' => 'completed'
        ];
    }

    protected function processCardPayment(Sale $sale, array $data): array
    {
        // In a real application, this would integrate with a payment gateway
        return [
            'method' => 'card',
            'amount_paid' => $sale->total,
            'change' => 0,
            'transaction_id' => uniqid('card_'),
            'status' => 'completed'
        ];
    }

    protected function processMixedPayment(Sale $sale, array $data): array
    {
        $totalPaid = $data['cash_amount'] + $data['card_amount'];

        if ($totalPaid < $sale->total) {
            throw new \Exception('Total payment amount is less than sale total');
        }

        $change = $totalPaid - $sale->total;

        return [
            'method' => 'mixed',
            'cash_amount' => $data['cash_amount'],
            'card_amount' => $data['card_amount'],
            'change' => $change,
            'transaction_id' => uniqid('mixed_'),
            'status' => 'completed'
        ];
    }

    protected function getPaymentInfo(Sale $sale): array
    {
        return [
            'subtotal' => $sale->subtotal,
            'tax' => $sale->tax,
            'total' => $sale->total,
            'paid_amount' => $sale->paid_amount,
            'payment_method' => $sale->payment_method,
            'payment_status' => $sale->payment_status,
            'change' => max(0, $sale->paid_amount - $sale->total)
        ];
    }

    protected function getCompanyInfo(): array
    {
        // In a real application, this would come from settings
        return [
            'name' => 'Your Company Name',
            'address' => 'Company Address',
            'phone' => 'Company Phone',
            'email' => 'company@email.com',
            'tax_number' => 'Tax Number',
            'logo' => 'path/to/logo.png'
        ];
    }
}