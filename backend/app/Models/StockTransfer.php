<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTransfer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'from_store_id',
        'to_store_id',
        'product_id',
        'quantity',
        'status',
        'reference_number',
        'initiated_by',
        'completed_by',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public static function generateReferenceNumber()
    {
        $prefix = 'TR';
        $year = date('Y');
        $month = date('m');
        
        $lastTransfer = self::where('reference_number', 'like', "{$prefix}-{$year}{$month}%")
            ->orderBy('reference_number', 'desc')
            ->first();

        if ($lastTransfer) {
            $sequence = (int) substr($lastTransfer->reference_number, -4);
            $sequence++;
        } else {
            $sequence = 1;
        }

        return sprintf("%s-%s%s%04d", $prefix, $year, $month, $sequence);
    }

    public function reservation()
    {
        return $this->morphOne(StockReservation::class, 'reference');
    }

    public function complete(User $user)
    {
        // Update transfer status
        $this->update([
            'status' => Store::TRANSFER_STATUS_COMPLETED,
            'completed_by' => $user->id,
            'completed_at' => now(),
        ]);

        // Complete the reservation
        if ($this->reservation) {
            $this->reservation->complete();
        }

        // Create stock movements
        StockMovement::create([
            'store_id' => $this->from_store_id,
            'product_id' => $this->product_id,
            'quantity' => -$this->quantity,
            'type' => 'transfer_out',
            'reference_type' => self::class,
            'reference_id' => $this->id,
        ]);

        StockMovement::create([
            'store_id' => $this->to_store_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'type' => 'transfer_in',
            'reference_type' => self::class,
            'reference_id' => $this->id,
        ]);
    }

    public function cancel()
    {
        // Update transfer status
        $this->update([
            'status' => Store::TRANSFER_STATUS_CANCELLED,
        ]);

        // Cancel the reservation
        if ($this->reservation) {
            $this->reservation->cancel();
        }
    }
}