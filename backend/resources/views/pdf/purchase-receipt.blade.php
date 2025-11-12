<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        .header { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $company['name'] ?? config('app.name') }}</h2>
        <div>{{ $company['address'] ?? '' }}</div>
        <div>{{ $company['phone'] ?? '' }} - {{ $company['email'] ?? '' }}</div>
    </div>

    <h3>Purchase Receipt: {{ $purchase->purchase_number }}</h3>
    <div>
        <strong>Supplier:</strong> {{ $purchase->supplier->name ?? 'N/A' }}<br>
        <strong>Date:</strong> {{ $purchase->purchase_date?->format('Y-m-d') ?? '' }}<br>
        <strong>Received Date:</strong> {{ $purchase->received_date?->format('Y-m-d') ?? '' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Ordered</th>
                <th>Received</th>
                <th>Unit Price</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->items as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ $item->received_quantity }}</td>
                <td class="right">{{ number_format($item->unit_price,2) }}</td>
                <td class="right">{{ number_format(($item->received_quantity * $item->unit_price),2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px; width:300px; float:right;">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="right">{{ number_format($purchase->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td class="right">{{ number_format($purchase->tax, 2) }}</td>
            </tr>
            @if($purchase->discount > 0)
            <tr>
                <td>Discount:</td>
                <td class="right">{{ number_format($purchase->discount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Total:</strong></td>
                <td class="right"><strong>{{ number_format($purchase->total, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear:both"></div>

    <div style="margin-top:30px;">
        <strong>Payments:</strong>
        <ul>
            @foreach($purchase->payments as $payment)
                <li>{{ $payment->method }} - {{ number_format($payment->amount,2) }} on {{ $payment->created_at->format('Y-m-d') }}</li>
            @endforeach
        </ul>
    </div>

    <div style="margin-top:30px; font-size:0.9em; color:#666;">
        This receipt confirms the goods received referenced by purchase order {{ $purchase->purchase_number }}.
    </div>
</body>
</html>