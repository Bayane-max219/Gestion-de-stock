<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    const TRANSFER_STATUS_PENDING = 'pending';
    const TRANSFER_STATUS_COMPLETED = 'completed';
    const TRANSFER_STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(StoreProduct::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }

    public function getProductQuantity($productId)
    {
        return $this->products()
            ->where('product_id', $productId)
            ->value('quantity') ?? 0;
    }

    public function updateProductQuantity($productId, $quantity, $type = 'add')
    {
        $storeProduct = $this->products()
            ->firstOrCreate(
                ['product_id' => $productId],
                ['quantity' => 0]
            );

        if ($type === 'add') {
            $storeProduct->increment('quantity', $quantity);
        } else {
            $storeProduct->decrement('quantity', $quantity);
        }

        return $storeProduct->refresh();
    }
}