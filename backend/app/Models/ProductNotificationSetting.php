<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductNotificationSetting extends Model
{
    protected $fillable = [
        'product_id',
        'store_id',
        'low_stock_threshold',
        'expiry_warning_days',
        'notify_on_low_stock',
        'notify_on_expiry',
    ];

    protected $casts = [
        'low_stock_threshold' => 'integer',
        'expiry_warning_days' => 'integer',
        'notify_on_low_stock' => 'boolean',
        'notify_on_expiry' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeWithLowStockNotifications($query)
    {
        return $query->where('notify_on_low_stock', true);
    }

    public function scopeWithExpiryNotifications($query)
    {
        return $query->where('notify_on_expiry', true);
    }
}