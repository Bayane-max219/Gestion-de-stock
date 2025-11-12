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
        .info {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
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
        .totals {
            width: 300px;
            float: right;
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 5px;
        }
        .totals .grand-total {
            font-weight: bold;
            font-size: 1.1em;
            background-color: #2C3E50;
            color: white;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
        .payment-info {
            margin-top: 30px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <div>{{ $company['address'] }}</div>
        <div>Tel: {{ $company['phone'] }} | Email: {{ $company['email'] }}</div>
        <div>Tax Number: {{ $company['tax_number'] }}</div>
    </div>

    <div class="info">
        <div class="info-row">
            <div>
                <strong>Invoice To:</strong><br>
                {{ $sale->client->name }}<br>
                {{ $sale->client->address }}<br>
                {{ $sale->client->phone }}<br>
                {{ $sale->client->email }}
            </div>
            <div>
                <strong>Invoice Number:</strong> {{ $sale->invoice_number }}<br>
                <strong>Date:</strong> {{ $sale->sale_date->format('d/m/Y') }}<br>
                <strong>Payment Status:</strong> {{ ucfirst($sale->payment_status) }}<br>
                <strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ number_format($item->quantity, 2) }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td align="right">{{ number_format($sale->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Tax ({{ config('app.tax_rate', 0.20) * 100 }}%):</td>
                <td align="right">{{ number_format($sale->tax, 2) }}</td>
            </tr>
            @if($sale->discount > 0)
            <tr>
                <td>Discount:</td>
                <td align="right">{{ number_format($sale->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>Total:</td>
                <td align="right">{{ number_format($sale->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($sale->notes)
    <div class="payment-info">
        <strong>Notes:</strong><br>
        {{ $sale->notes }}
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        @if($sale->payment_status !== 'paid')
        <p>Please make your payment within the agreed payment terms.</p>
        @endif
    </div>
</body>
</html>