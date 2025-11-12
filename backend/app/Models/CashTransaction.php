<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashTransaction extends Model
{
    use HasFactory, HasUuids;

    const TYPE_SALE = 'sale';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    protected $fillable = [
        'cash_register_id',
        'user_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'description',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Ensure expense and purchase amounts are stored as negative
            if (in_array($transaction->type, [self::TYPE_EXPENSE, self::TYPE_PURCHASE])) {
                $transaction->amount = -abs($transaction->amount);
            } else {
                $transaction->amount = abs($transaction->amount);
            }
        });

        static::created(function ($transaction) {
            // Update reference payment status if applicable
            if ($transaction->reference) {
                switch (get_class($transaction->reference)) {
                    case Sale::class:
                        $transaction->reference->updatePaymentStatus();
                        break;
                    case Purchase::class:
                        $transaction->reference->updatePaymentStatus();
                        break;
                }
            }
        });
    }

    public function getFormattedAmountAttribute()
    {
        $symbol = $this->amount < 0 ? '-' : '+';
        return $symbol . ' ' . number_format(abs($this->amount), 2);
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            self::TYPE_SALE => 'Sale Payment',
            self::TYPE_PURCHASE => 'Purchase Payment',
            self::TYPE_INCOME => 'Other Income',
            self::TYPE_EXPENSE => 'Expense',
            default => ucfirst($this->type),
        };
    }

    public function getReferenceNumberAttribute()
    {
        if (!$this->reference) {
            return null;
        }

        return match(get_class($this->reference)) {
            Sale::class => $this->reference->invoice_number,
            Purchase::class => $this->reference->purchase_number,
            default => null,
        };
    }

    public static function recordSalePayment($sale, $amount, $cashRegisterId, $userId)
    {
        return self::create([
            'cash_register_id' => $cashRegisterId,
            'user_id' => $userId,
            'type' => self::TYPE_SALE,
            'amount' => $amount,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'description' => "Payment for invoice #{$sale->invoice_number}",
            'payment_method' => 'cash',
        ]);
    }

    public static function recordPurchasePayment($purchase, $amount, $cashRegisterId, $userId)
    {
        return self::create([
            'cash_register_id' => $cashRegisterId,
            'user_id' => $userId,
            'type' => self::TYPE_PURCHASE,
            'amount' => -$amount,
            'reference_type' => Purchase::class,
            'reference_id' => $purchase->id,
            'description' => "Payment for PO #{$purchase->purchase_number}",
            'payment_method' => 'cash',
        ]);
    }

    public function validateTransaction()
    {
        // Validate payment method
        if (!in_array($this->payment_method, ['cash', 'card', 'bank_transfer'])) {
            throw new \Exception('Invalid payment method');
        }

        // Validate type
        if (!in_array($this->type, [
            self::TYPE_SALE,
            self::TYPE_PURCHASE,
            self::TYPE_INCOME,
            self::TYPE_EXPENSE
        ])) {
            throw new \Exception('Invalid transaction type');
        }

        // Validate reference for sales and purchases
        if (in_array($this->type, [self::TYPE_SALE, self::TYPE_PURCHASE])) {
            if (!$this->reference_type || !$this->reference_id) {
                throw new \Exception('Reference is required for sales and purchases');
            }
        }

        // Validate amount
        if ($this->amount == 0) {
            throw new \Exception('Amount cannot be zero');
        }

        return true;
    }
}