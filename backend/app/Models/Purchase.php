<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory, HasUuids;

    const STATUS_PENDING = 'pending';
    const STATUS_RECEIVED = 'received';
    const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_PARTIALLY_PAID = 'partially_paid';

    protected $fillable = [
        'store_id',
        'supplier_id',
        'user_id',
        'purchase_number',
        'purchase_date',
        'expected_date',
        'received_date',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'payment_status',
        'payment_due_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'expected_date' => 'datetime',
        'received_date' => 'datetime',
        'payment_due_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public function getTotalReceivedAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->received_quantity;
        });
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->total - $this->total_paid;
    }

    public function generatePurchaseNumber()
    {
        $prefix = 'PO';
        $year = date('Y');
        $month = date('m');
        
        $lastPurchase = self::where('purchase_number', 'like', "{$prefix}-{$year}{$month}%")
            ->orderBy('purchase_number', 'desc')
            ->first();

        if ($lastPurchase) {
            $sequence = (int) substr($lastPurchase->purchase_number, -4);
            $sequence++;
        } else {
            $sequence = 1;
        }

        $this->purchase_number = sprintf("%s-%s%s%04d", $prefix, $year, $month, $sequence);
        return $this->purchase_number;
    }

    public function updateStatus()
    {
        $totalItems = $this->items()->count();
        $totalReceived = $this->items()
            ->where('received_quantity', '>', 0)
            ->count();

        if ($totalReceived === 0) {
            $this->status = self::STATUS_PENDING;
        } elseif ($totalReceived === $totalItems && $this->items()->where('received_quantity', '<', DB::raw('quantity'))->count() === 0) {
            $this->status = self::STATUS_RECEIVED;
            $this->received_date = now();
        } else {
            $this->status = self::STATUS_PARTIALLY_RECEIVED;
        }

        $this->save();
    }

    public function updatePaymentStatus()
    {
        $totalPaid = $this->total_paid;

        if ($totalPaid >= $this->total) {
            $this->payment_status = self::PAYMENT_STATUS_PAID;
        } elseif ($totalPaid > 0) {
            $this->payment_status = self::PAYMENT_STATUS_PARTIALLY_PAID;
        } else {
            $this->payment_status = self::PAYMENT_STATUS_PENDING;
        }

        $this->save();
    }

    public function receiveItems(array $receivedItems)
    {
        DB::beginTransaction();

        try {
            foreach ($receivedItems as $itemData) {
                $item = $this->items()->find($itemData['id']);
                
                if (!$item) {
                    throw new \Exception("Purchase item not found");
                }

                $newQuantity = $itemData['received_quantity'];
                $oldQuantity = $item->received_quantity;
                $difference = $newQuantity - $oldQuantity;

                if ($difference != 0) {
                    // Update received quantity
                    $item->received_quantity = $newQuantity;
                    $item->save();

                    // Create stock movement
                    if ($difference > 0) {
                        $item->product->incrementStock(
                            $this->store_id,
                            $difference,
                            'purchase_received',
                            $this
                        );
                    } else {
                        $item->product->decrementStock(
                            $this->store_id,
                            abs($difference),
                            'purchase_correction',
                            $this
                        );
                    }
                }
            }

            $this->updateStatus();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}