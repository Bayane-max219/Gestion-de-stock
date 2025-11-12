@component('mail::message')
# {{ $notification->type === 'daily_summary' ? 'Daily Summary Report' : 'New Notification' }}

@if($notification->type === 'low_stock')
**Low Stock Alert**

The following product is running low on stock:
- Product: {{ $data['product_name'] }}
- Current Quantity: {{ $data['current_quantity'] }}
- Minimum Quantity: {{ $data['min_quantity'] }}

@elseif($notification->type === 'expiring_product')
**Product Expiration Alert**

The following product is nearing its expiration date:
- Product: {{ $data['product_name'] }}
- Expiration Date: {{ $data['expiry_date'] }}
- Current Quantity: {{ $data['current_quantity'] }}

@elseif($notification->type === 'daily_summary')
**Daily Operations Summary**

Sales:
- Total Orders: {{ $data['sales']['count'] }}
- Total Amount: {{ number_format($data['sales']['total'], 2) }}

Purchases:
- Total Orders: {{ $data['purchases']['count'] }}
- Total Amount: {{ number_format($data['purchases']['total'], 2) }}

Stock:
- Stock Movements: {{ $data['stock_movements'] }}
- Low Stock Items: {{ $data['low_stock_items'] }}

@else
{{ $notification->data['message'] ?? 'You have a new notification.' }}
@endif

@component('mail::button', ['url' => $actionUrl])
View Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@component('mail::subcopy')
This is an automated notification from {{ config('app.name') }}. If you have any questions, please contact support.
@endcomponent
@endcomponent