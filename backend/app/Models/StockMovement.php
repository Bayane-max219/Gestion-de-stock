<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory, HasUuids;

    const TYPE_SALE = 'sale';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_RETURN = 'return';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_TRANSFER = 'transfer';

    protected $fillable = [
        'store_id',
        'product_id',
        'movement_type',
        'quantity',
        'reference_id',
        'reference_type',
        'notes',
    ];

    public $timestamps = true;

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public static function recordMovement($storeId, $productId, $type, $quantity, $referenceId = null, $referenceType = null, $notes = null)
    {
        return static::create([
            'store_id' => $storeId,
            'product_id' => $productId,
            'movement_type' => $type,
            'quantity' => $quantity,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'notes' => $notes,
        ]);
    }
}