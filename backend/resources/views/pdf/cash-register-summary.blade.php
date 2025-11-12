<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cash Register Summary</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .register-info {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .summary-table th {
            background-color: #f5f5f5;
        }
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .transactions-table th,
        .transactions-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .transactions-table th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .amount {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $company['name'] }}</h2>
        <p>{{ $company['address'] }}</p>
        <p>Tel: {{ $company['phone'] }} | Email: {{ $company['email'] }}</p>
        <h3>Cash Register Summary</h3>
    </div>

    <div class="register-info">
        <table width="100%">
            <tr>
                <td width="50%">
                    <strong>Register ID:</strong> #{{ $register->id }}<br>
                    <strong>Store:</strong> {{ $register->store->name }}<br>
                    <strong>Cashier:</strong> {{ $register->user->name }}
                </td>
                <td width="50%">
                    <strong>Opening Date:</strong> {{ $register->opening_date->format('Y-m-d H:i') }}<br>
                    <strong>Status:</strong> {{ ucfirst($register->status) }}<br>
                    @if($register->closing_date)
                    <strong>Closing Date:</strong> {{ $register->closing_date->format('Y-m-d H:i') }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <h4>Balance Summary</h4>
    <table class="summary-table">
        <tr>
            <th>Description</th>
            <th class="amount">Amount</th>
        </tr>
        <tr>
            <td>Opening Balance</td>
            <td class="amount">{{ number_format($register->opening_balance, 2) }}</td>
        </tr>
        <tr>
            <td>Cash Sales</td>
            <td class="amount">{{ number_format($summary['total_sales'], 2) }}</td>
        </tr>
        <tr>
            <td>Cash Purchases</td>
            <td class="amount">{{ number_format($summary['total_purchases'], 2) }}</td>
        </tr>
        <tr>
            <td>Other Income</td>
            <td class="amount">{{ number_format($summary['total_income'], 2) }}</td>
        </tr>
        <tr>
            <td>Other Expenses</td>
            <td class="amount">{{ number_format($summary['total_expenses'], 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Expected Balance</td>
            <td class="amount">{{ number_format($register->expected_balance, 2) }}</td>
        </tr>
        @if($register->status === 'closed')
        <tr>
            <td>Actual Closing Balance</td>
            <td class="amount">{{ number_format($register->actual_closing_balance, 2) }}</td>
        </tr>
        <tr>
            <td>Difference</td>
            <td class="amount">{{ number_format($register->difference, 2) }}</td>
        </tr>
        @endif
    </table>

    <h4>Transaction History</h4>
    <table class="transactions-table">
        <tr>
            <th>Time</th>
            <th>Type</th>
            <th>Description</th>
            <th>Method</th>
            <th class="amount">Amount</th>
            <th class="amount">Balance</th>
            <th>User</th>
        </tr>
        @foreach($register->transactions as $transaction)
        <tr>
            <td>{{ $transaction->created_at->format('H:i') }}</td>
            <td>{{ ucfirst($transaction->type) }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ ucfirst($transaction->payment_method) }}</td>
            <td class="amount">{{ number_format($transaction->amount, 2) }}</td>
            <td class="amount">{{ number_format($transaction->balance_after, 2) }}</td>
            <td>{{ $transaction->user->name }}</td>
        </tr>
        @endforeach
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>This is a computer generated document</p>
    </div>
</body>
</html>