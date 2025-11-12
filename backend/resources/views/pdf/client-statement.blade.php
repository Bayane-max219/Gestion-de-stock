<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2C3E50;
        }
        .client-info {
            margin-bottom: 30px;
        }
        .statement-details {
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th {
            background-color: #2C3E50;
            color: white;
            text-align: left;
            padding: 10px;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .overdue {
            color: #E74C3C;
        }
        .summary {
            margin-top: 30px;
            float: right;
            width: 300px;
        }
        .summary table {
            width: 100%;
        }
        .summary td {
            padding: 5px;
        }
        .summary .total {
            font-weight: bold;
            background-color: #2C3E50;
            color: white;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <div>{{ $company['address'] }}</div>
        <div>Tel: {{ $company['phone'] }} | Email: {{ $company['email'] }}</div>
    </div>

    <div class="client-info">
        <h2>Statement of Account</h2>
        <strong>Client:</strong> {{ $client->name }}<br>
        <strong>Code:</strong> {{ $client->code }}<br>
        <strong>Address:</strong> {{ $client->address }}<br>
        <strong>Phone:</strong> {{ $client->phone }}<br>
        <strong>Email:</strong> {{ $client->email }}
    </div>

    <div class="statement-details">
        <strong>Statement Date:</strong> {{ now()->format('d/m/Y') }}<br>
        <strong>Payment Terms:</strong> {{ $client->payment_terms }} days<br>
        <strong>Credit Limit:</strong> {{ number_format($client->credit_limit, 2) }}<br>
        <strong>Available Credit:</strong> {{ number_format($client->available_credit, 2) }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Date</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($client->sales as $sale)
            @php
                $dueDate = $sale->sale_date->addDays($client->payment_terms);
                $isOverdue = $dueDate < now() && $sale->payment_status !== 'paid';
            @endphp
            <tr @if($isOverdue) class="overdue" @endif>
                <td>{{ $sale->invoice_number }}</td>
                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                <td>{{ $dueDate->format('d/m/Y') }}</td>
                <td>{{ number_format($sale->total, 2) }}</td>
                <td>{{ number_format($sale->payments->sum('amount'), 2) }}</td>
                <td>{{ number_format($sale->total - $sale->payments->sum('amount'), 2) }}</td>
                <td>{{ ucfirst($sale->payment_status) }}@if($isOverdue) (Overdue) @endif</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Total Sales:</td>
                <td align="right">{{ number_format($client->total_sales, 2) }}</td>
            </tr>
            <tr>
                <td>Total Paid:</td>
                <td align="right">{{ number_format($client->total_paid, 2) }}</td>
            </tr>
            <tr class="total">
                <td>Balance Due:</td>
                <td align="right">{{ number_format($client->total_due, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>This statement includes all unpaid invoices as of {{ now()->format('d/m/Y') }}.</p>
        @if($client->total_due > 0)
        <p>Please arrange payment for overdue invoices as soon as possible.</p>
        <p>For questions about this statement, please contact our accounting department.</p>
        @endif
    </div>
</body>
</html>