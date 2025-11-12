<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockReservation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'store_id',
        'quantity',
        'reference_type',
        'reference_id',
        'status',
        'expires_at',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    protected $casts = [
        'quantity' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && !$this->isExpired();
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function complete()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function cancel()
    {
        if ($this->status === self::STATUS_ACTIVE) {
            $this->update(['status' => self::STATUS_CANCELLED]);
            // Release reserved stock
            $this->store->updateProductQuantity($this->product_id, $this->quantity, 'add');
        }
    }
}