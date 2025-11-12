<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Client;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getSummary($timeRange = 'today', $storeId = null)
    {
        $query = function ($model) use ($timeRange, $storeId) {
            $query = $model::query();
            
            if ($storeId) {
                $query->where('store_id', $storeId);
            }

            switch ($timeRange) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
            }

            return $query;
        };

        // Calculate totals
        $totalSales = $query(Sale::class)->sum('total_amount');
        $totalPurchases = $query(Purchase::class)->sum('total_amount');
        $grossMargin = $totalSales - $totalPurchases;

        // Get current cash balance
        $cashBalance = CashRegister::when($storeId, function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        })->where('status', 'open')->sum('current_balance');

        // Count customers and low stock items
        $customerCount = $query(Client::class)->count();
        $lowStockCount = Product::when($storeId, function ($q) use ($storeId) {
            $q->whereHas('storeProducts', function ($q) use ($storeId) {
                $q->where('store_id', $storeId)
                    ->whereRaw('quantity <= min_quantity');
            });
        })->count();

        return [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'gross_margin' => $grossMargin,
            'cash_balance' => $cashBalance,
            'customer_count' => $customerCount,
            'low_stock_count' => $lowStockCount,
            'time_range' => $timeRange,
        ];
    }

    public function getSalesAnalytics($period = 'monthly', $year = null, $month = null, $storeId = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $query = Sale::query()
            ->when($storeId, function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });

        if ($period === 'monthly') {
            $data = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            return [
                'labels' => $data->pluck('date'),
                'datasets' => [
                    [
                        'label' => 'Sales Amount',
                        'data' => $data->pluck('total'),
                    ],
                    [
                        'label' => 'Number of Sales',
                        'data' => $data->pluck('count'),
                    ],
                ],
            ];
        }

        // Yearly data
        $data = $query->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return [
            'labels' => $data->pluck('month')->map(function ($month) {
                return Carbon::create()->month($month)->format('F');
            }),
            'datasets' => [
                [
                    'label' => 'Sales Amount',
                    'data' => $data->pluck('total'),
                ],
                [
                    'label' => 'Number of Sales',
                    'data' => $data->pluck('count'),
                ],
            ],
        ];
    }

    public function getPurchasesAnalytics($period = 'monthly', $year = null, $month = null, $storeId = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $query = Purchase::query()
            ->when($storeId, function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });

        if ($period === 'monthly') {
            $data = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            return [
                'labels' => $data->pluck('date'),
                'datasets' => [
                    [
                        'label' => 'Purchase Amount',
                        'data' => $data->pluck('total'),
                    ],
                    [
                        'label' => 'Number of Purchases',
                        'data' => $data->pluck('count'),
                    ],
                ],
            ];
        }

        // Yearly data
        $data = $query->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return [
            'labels' => $data->pluck('month')->map(function ($month) {
                return Carbon::create()->month($month)->format('F');
            }),
            'datasets' => [
                [
                    'label' => 'Purchase Amount',
                    'data' => $data->pluck('total'),
                ],
                [
                    'label' => 'Number of Purchases',
                    'data' => $data->pluck('count'),
                ],
            ],
        ];
    }

    public function getStockAnalytics($storeId = null, $categoryId = null)
    {
        $query = Category::query()
            ->withSum(['products as total_value' => function ($q) use ($storeId) {
                $q->when($storeId, function ($q) use ($storeId) {
                    $q->whereHas('storeProducts', function ($q) use ($storeId) {
                        $q->where('store_id', $storeId);
                    });
                });
            }], DB::raw('quantity * cost_price'))
            ->withCount(['products as product_count' => function ($q) use ($storeId) {
                $q->when($storeId, function ($q) use ($storeId) {
                    $q->whereHas('storeProducts', function ($q) use ($storeId) {
                        $q->where('store_id', $storeId);
                    });
                });
            }])
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });

        $data = $query->get();

        return [
            'labels' => $data->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Stock Value',
                    'data' => $data->pluck('total_value'),
                ],
                [
                    'label' => 'Product Count',
                    'data' => $data->pluck('product_count'),
                ],
            ],
        ];
    }

    public function getCashflowAnalytics($period = 'monthly', $year = null, $month = null, $storeId = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $query = DB::table('cash_transactions')
            ->join('cash_registers', 'cash_transactions.cash_register_id', '=', 'cash_registers.id')
            ->when($storeId, function ($q) use ($storeId) {
                $q->where('cash_registers.store_id', $storeId);
            });

        if ($period === 'monthly') {
            $data = $query->select(
                DB::raw('DATE(cash_transactions.created_at) as date'),
                DB::raw('SUM(CASE WHEN type IN ("sale", "income") THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type IN ("purchase", "expense") THEN ABS(amount) ELSE 0 END) as expense')
            )
            ->whereYear('cash_transactions.created_at', $year)
            ->whereMonth('cash_transactions.created_at', $month)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            return [
                'labels' => $data->pluck('date'),
                'datasets' => [
                    [
                        'label' => 'Income',
                        'data' => $data->pluck('income'),
                    ],
                    [
                        'label' => 'Expense',
                        'data' => $data->pluck('expense'),
                    ],
                ],
            ];
        }

        // Yearly data
        $data = $query->select(
            DB::raw('MONTH(cash_transactions.created_at) as month'),
            DB::raw('SUM(CASE WHEN type IN ("sale", "income") THEN amount ELSE 0 END) as income'),
            DB::raw('SUM(CASE WHEN type IN ("purchase", "expense") THEN ABS(amount) ELSE 0 END) as expense')
        )
        ->whereYear('cash_transactions.created_at', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return [
            'labels' => $data->pluck('month')->map(function ($month) {
                return Carbon::create()->month($month)->format('F');
            }),
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $data->pluck('income'),
                ],
                [
                    'label' => 'Expense',
                    'data' => $data->pluck('expense'),
                ],
            ],
        ];
    }

    public function getTopProducts($limit = 10, $period = 'monthly', $storeId = null)
    {
        $query = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total_amount) as total_amount')
            )
            ->when($storeId, function ($q) use ($storeId) {
                $q->where('sales.store_id', $storeId);
            });

        switch ($period) {
            case 'weekly':
                $query->whereBetween('sales.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('sales.created_at', Carbon::now()->month)
                    ->whereYear('sales.created_at', Carbon::now()->year);
                break;
            case 'yearly':
                $query->whereYear('sales.created_at', Carbon::now()->year);
                break;
        }

        $data = $query->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();

        return [
            'labels' => $data->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Quantity Sold',
                    'data' => $data->pluck('total_quantity'),
                ],
                [
                    'label' => 'Sales Amount',
                    'data' => $data->pluck('total_amount'),
                ],
            ],
            'details' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'quantity' => $item->total_quantity,
                    'amount' => $item->total_amount,
                ];
            }),
        ];
    }

    public function generateSummaryCharts($timeRange, $storeId = null)
    {
        return [
            'sales_evolution' => $this->getSalesAnalytics('monthly', null, null, $storeId),
            'top_products' => $this->getTopProducts(10, 'monthly', $storeId),
            'stock_distribution' => $this->getStockAnalytics($storeId),
            'profit_expense' => $this->getCashflowAnalytics('monthly', null, null, $storeId),
        ];
    }
}