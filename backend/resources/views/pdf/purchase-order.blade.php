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
        .delivery-info {
            margin-top: 30px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            width: 45%;
            text-align: center;
        }
        .signature-line hr {
            margin-top: 50px;
            border-top: 1px solid #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <div>{{ $company['address'] }}</div>
        <div>Tel: {{ $company['phone'] }} | Email: {{ $company['email'] }}</div>
        <div>Tax Number: {{ $company['tax_number'] }}</div>
        <h2>Purchase Order</h2>
    </div>

    <div class="info">
        <div class="info-row">
            <div>
                <strong>Supplier:</strong><br>
                {{ $purchase->supplier->name }}<br>
                {{ $purchase->supplier->address }}<br>
                {{ $purchase->supplier->phone }}<br>
                {{ $purchase->supplier->email }}
            </div>
            <div>
                <strong>PO Number:</strong> {{ $purchase->purchase_number }}<br>
                <strong>Date:</strong> {{ $purchase->purchase_date->format('d/m/Y') }}<br>
                <strong>Expected Date:</strong> {{ $purchase->expected_date->format('d/m/Y') }}<br>
                <strong>Payment Terms:</strong> {{ $purchase->supplier->payment_terms }} days
            </div>
        </div>
    </div>

    <div class="delivery-info">
        <strong>Delivery Address:</strong><br>
        {{ $purchase->store->name }}<br>
        {{ $purchase->store->address }}<br>
        {{ $purchase->store->phone }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Code</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->code }}</td>
                <td>{{ number_format($item->quantity, 2) }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->tax, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td align="right">{{ number_format($purchase->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td align="right">{{ number_format($purchase->tax, 2) }}</td>
            </tr>
            @if($purchase->discount > 0)
            <tr>
                <td>Discount:</td>
                <td align="right">{{ number_format($purchase->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>Total:</td>
                <td align="right">{{ number_format($purchase->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($purchase->notes)
    <div class="delivery-info">
        <strong>Notes:</strong><br>
        {{ $purchase->notes }}
    </div>
    @endif

    <div class="signatures">
        <div class="signature-line">
            <hr>
            Authorized By
        </div>
        <div class="signature-line">
            <hr>
            Supplier Confirmation
        </div>
    </div>

    <div class="footer">
        <p>This is a computer generated document. No signature is required.</p>
        <p>Please send the invoice referencing this PO number.</p>
    </div>
</body>
</html>