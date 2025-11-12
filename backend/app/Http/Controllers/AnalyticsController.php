<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Client;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AnalyticsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\PDF;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function summary(Request $request)
    {
        $timeRange = $request->input('time_range', 'today');
        $storeId = $request->input('store_id');

        $summary = $this->analyticsService->getSummary($timeRange, $storeId);

        return response()->json($summary);
    }

    public function sales(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $storeId = $request->input('store_id');

        $salesData = $this->analyticsService->getSalesAnalytics($period, $year, $month, $storeId);

        return response()->json($salesData);
    }

    public function purchases(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $storeId = $request->input('store_id');

        $purchasesData = $this->analyticsService->getPurchasesAnalytics($period, $year, $month, $storeId);

        return response()->json($purchasesData);
    }

    public function stock(Request $request)
    {
        $storeId = $request->input('store_id');
        $categoryId = $request->input('category_id');

        $stockData = $this->analyticsService->getStockAnalytics($storeId, $categoryId);

        return response()->json($stockData);
    }

    public function cashflow(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $storeId = $request->input('store_id');

        $cashflowData = $this->analyticsService->getCashflowAnalytics($period, $year, $month, $storeId);

        return response()->json($cashflowData);
    }

    public function topProducts(Request $request)
    {
        $limit = $request->input('limit', 10);
        $period = $request->input('period', 'monthly');
        $storeId = $request->input('store_id');

        $topProducts = $this->analyticsService->getTopProducts($limit, $period, $storeId);

        return response()->json($topProducts);
    }

    public function exportSummaryPdf(Request $request)
    {
        $timeRange = $request->input('time_range', 'today');
        $storeId = $request->input('store_id');

        $summary = $this->analyticsService->getSummary($timeRange, $storeId);
        $charts = $this->analyticsService->generateSummaryCharts($timeRange, $storeId);

        $pdf = PDF::loadView('pdf.analytics-summary', [
            'summary' => $summary,
            'charts' => $charts,
            'timeRange' => $timeRange,
            'generatedAt' => now(),
            'company' => [
                'name' => config('app.name'),
                'address' => config('app.address'),
                'phone' => config('app.phone'),
                'email' => config('app.email'),
            ],
        ]);

        return $pdf->download('analytics-summary.pdf');
    }

    public function exportExcel(Request $request)
    {
        $type = $request->input('type', 'sales');
        $period = $request->input('period', 'monthly');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $storeId = $request->input('store_id');

        return Excel::download(
            new AnalyticsExport($type, $period, $year, $month, $storeId),
            "analytics-{$type}-{$period}.xlsx"
        );
    }
}