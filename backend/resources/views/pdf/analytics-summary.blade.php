<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Analytics Summary</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .summary-section {
            margin-bottom: 30px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .kpi-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c5282;
        }
        .kpi-label {
            font-size: 14px;
            color: #666;
        }
        .chart-section {
            margin-bottom: 40px;
        }
        .chart-title {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .amount {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .chart-container {
            margin: 20px 0;
            border: 1px solid #eee;
            padding: 10px;
        }
        .positive {
            color: #38a169;
        }
        .negative {
            color: #e53e3e;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <p>{{ $company['address'] }}</p>
        <p>Tel: {{ $company['phone'] }} | Email: {{ $company['email'] }}</p>
        <h2>Analytics Summary Report</h2>
        <p>Period: {{ ucfirst($timeRange) }}</p>
        <p>Generated on: {{ $generatedAt->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary-section">
        <h3>Key Performance Indicators</h3>
        <div class="summary-grid">
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($summary['total_sales'], 2) }}</div>
                <div class="kpi-label">Total Sales</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($summary['total_purchases'], 2) }}</div>
                <div class="kpi-label">Total Purchases</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value {{ $summary['gross_margin'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($summary['gross_margin'], 2) }}
                </div>
                <div class="kpi-label">Gross Margin</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($summary['cash_balance'], 2) }}</div>
                <div class="kpi-label">Cash Balance</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($summary['customer_count']) }}</div>
                <div class="kpi-label">Total Customers</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value {{ $summary['low_stock_count'] > 0 ? 'negative' : '' }}">
                    {{ number_format($summary['low_stock_count']) }}
                </div>
                <div class="kpi-label">Low Stock Items</div>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <h3>Sales Evolution</h3>
        <table>
            <tr>
                <th>Period</th>
                <th class="amount">Amount</th>
                <th>Count</th>
            </tr>
            @foreach($charts['sales_evolution']['labels'] as $index => $label)
            <tr>
                <td>{{ $label }}</td>
                <td class="amount">{{ number_format($charts['sales_evolution']['datasets'][0]['data'][$index], 2) }}</td>
                <td>{{ $charts['sales_evolution']['datasets'][1]['data'][$index] }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="chart-section">
        <h3>Top 10 Best Selling Products</h3>
        <table>
            <tr>
                <th>Product</th>
                <th class="amount">Quantity</th>
                <th class="amount">Amount</th>
            </tr>
            @foreach($charts['top_products']['details'] as $product)
            <tr>
                <td>{{ $product['name'] }} ({{ $product['sku'] }})</td>
                <td class="amount">{{ number_format($product['quantity']) }}</td>
                <td class="amount">{{ number_format($product['amount'], 2) }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="chart-section">
        <h3>Stock Value Distribution</h3>
        <table>
            <tr>
                <th>Category</th>
                <th class="amount">Value</th>
                <th>Products</th>
            </tr>
            @foreach($charts['stock_distribution']['labels'] as $index => $label)
            <tr>
                <td>{{ $label }}</td>
                <td class="amount">{{ number_format($charts['stock_distribution']['datasets'][0]['data'][$index], 2) }}</td>
                <td>{{ $charts['stock_distribution']['datasets'][1]['data'][$index] }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="chart-section">
        <h3>Income vs Expenses</h3>
        <table>
            <tr>
                <th>Period</th>
                <th class="amount">Income</th>
                <th class="amount">Expenses</th>
                <th class="amount">Net</th>
            </tr>
            @foreach($charts['profit_expense']['labels'] as $index => $label)
            @php
                $income = $charts['profit_expense']['datasets'][0]['data'][$index];
                $expense = $charts['profit_expense']['datasets'][1]['data'][$index];
                $net = $income - $expense;
            @endphp
            <tr>
                <td>{{ $label }}</td>
                <td class="amount">{{ number_format($income, 2) }}</td>
                <td class="amount">{{ number_format($expense, 2) }}</td>
                <td class="amount {{ $net >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($net, 2) }}
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="footer">
        <p>This report was automatically generated by the system. All figures are subject to verification.</p>
    </div>
</body>
</html>