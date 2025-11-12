<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'received_quantity',
        'unit_price',
        'subtotal',
        'tax',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->calculateTotals();
        });

        static::updating(function ($item) {
            $item->calculateTotals();
        });

        static::deleted(function ($item) {
            // If item was received, decrease stock accordingly
            if ($item->received_quantity > 0) {
                $item->product->decrementStock(
                    $item->purchase->store_id,
                    $item->received_quantity,
                    'purchase_item_deleted',
                    $item->purchase
                );
            }
        });
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        $this->tax = $this->subtotal * config('app.tax_rate', 0.20);
        $this->total = $this->subtotal + $this->tax;
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->received_quantity;
    }

    public function isFullyReceived()
    {
        return $this->remaining_quantity <= 0;
    }

    public function receive($quantity)
    {
        if ($quantity > $this->remaining_quantity) {
            throw new \Exception("Cannot receive more than remaining quantity");
        }

        $this->received_quantity += $quantity;
        $this->save();

        // Create stock movement
        $this->product->incrementStock(
            $this->purchase->store_id,
            $quantity,
            'purchase_received',
            $this->purchase
        );
    }

    public function updatePriceHistory()
    {
        // Update product's purchase price if this is the latest purchase
        $latestPurchase = $this->product->purchaseItems()
            ->whereHas('purchase', function ($query) {
                $query->where('status', Purchase::STATUS_RECEIVED);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestPurchase && $latestPurchase->id === $this->id) {
            $this->product->update([
                'purchase_price' => $this->unit_price,
            ]);
        }
    }
}