<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CashRegister extends Model
{
    use HasFactory, HasUuids;

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'store_id',
        'user_id',
        'opening_date',
        'closing_date',
        'opening_balance',
        'expected_closing_balance',
        'actual_closing_balance',
        'difference',
        'status',
        'notes',
    ];

    protected $casts = [
        'opening_date' => 'datetime',
        'closing_date' => 'datetime',
        'opening_balance' => 'decimal:2',
        'expected_closing_balance' => 'decimal:2',
        'actual_closing_balance' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function sales()
    {
        return $this->hasManyThrough(Sale::class, CashTransaction::class, 'cash_register_id', 'id', 'id', 'reference_id')
            ->where('reference_type', Sale::class);
    }

    public function purchases()
    {
        return $this->hasManyThrough(Purchase::class, CashTransaction::class, 'cash_register_id', 'id', 'id', 'reference_id')
            ->where('reference_type', Purchase::class);
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->opening_balance + $this->transactions()->sum('amount');
    }

    public function getTotalSalesAttribute()
    {
        return $this->transactions()
            ->where('type', CashTransaction::TYPE_SALE)
            ->sum('amount');
    }

    public function getTotalPurchasesAttribute()
    {
        return abs($this->transactions()
            ->where('type', CashTransaction::TYPE_PURCHASE)
            ->sum('amount'));
    }

    public function getTotalIncomeAttribute()
    {
        return $this->transactions()
            ->where('type', CashTransaction::TYPE_INCOME)
            ->sum('amount');
    }

    public function getTotalExpenseAttribute()
    {
        return abs($this->transactions()
            ->where('type', CashTransaction::TYPE_EXPENSE)
            ->sum('amount'));
    }

    public static function getCurrentRegister($store_id)
    {
        return self::where('store_id', $store_id)
            ->where('status', self::STATUS_OPEN)
            ->latest()
            ->first();
    }

    public static function openRegister($data)
    {
        // Check if there's already an open register
        if (self::getCurrentRegister($data['store_id'])) {
            throw new \Exception('There is already an open cash register for this store');
        }

        return self::create([
            'store_id' => $data['store_id'],
            'user_id' => $data['user_id'],
            'opening_date' => now(),
            'opening_balance' => $data['opening_balance'],
            'status' => self::STATUS_OPEN,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function closeRegister($data)
    {
        if ($this->status !== self::STATUS_OPEN) {
            throw new \Exception('This register is already closed');
        }

        // Calculate expected closing balance
        $this->expected_closing_balance = $this->current_balance;
        $this->actual_closing_balance = $data['actual_closing_balance'];
        $this->difference = $this->actual_closing_balance - $this->expected_closing_balance;
        $this->closing_date = now();
        $this->status = self::STATUS_CLOSED;
        $this->notes = ($this->notes ? $this->notes . "\n" : '') . 
            ($data['notes'] ?? '');

        // If difference is too large, require confirmation
        if (abs($this->difference) > config('app.max_cash_difference', 10)) {
            if (!($data['confirm_difference'] ?? false)) {
                throw new \Exception('Large difference detected. Please confirm to close register.');
            }
        }

        $this->save();

        // If difference is significant, create notification
        if (abs($this->difference) > config('app.suspicious_cash_difference', 50)) {
            // Create notification for admin
            $notification = new Notification([
                'type' => 'cash_difference',
                'user_id' => null, // For all admins
                'title' => 'Suspicious Cash Register Difference',
                'message' => "Cash register #{$this->id} closed with a difference of {$this->difference}",
                'data' => [
                    'cash_register_id' => $this->id,
                    'difference' => $this->difference,
                    'store_id' => $this->store_id,
                    'user_id' => $this->user_id,
                ],
            ]);
            $notification->save();
        }

        return $this;
    }

    public function recordTransaction($data)
    {
        if ($this->status !== self::STATUS_OPEN) {
            throw new \Exception('Cannot record transaction on closed register');
        }

        // Check for negative balance if it's an expense
        if ($data['type'] === CashTransaction::TYPE_EXPENSE || 
            $data['type'] === CashTransaction::TYPE_PURCHASE) {
            $newBalance = $this->current_balance + $data['amount'];
            if ($newBalance < 0) {
                throw new \Exception('Insufficient funds in cash register');
            }
        }

        // Check for suspicious large expenses
        if ($data['type'] === CashTransaction::TYPE_EXPENSE && 
            abs($data['amount']) > config('app.suspicious_expense_amount', 1000)) {
            // Create notification for admin
            $notification = new Notification([
                'type' => 'large_expense',
                'user_id' => null, // For all admins
                'title' => 'Large Expense Detected',
                'message' => "Large expense of {$data['amount']} recorded in store #{$this->store_id}",
                'data' => [
                    'cash_register_id' => $this->id,
                    'amount' => $data['amount'],
                    'description' => $data['description'],
                    'store_id' => $this->store_id,
                    'user_id' => $data['user_id'],
                ],
            ]);
            $notification->save();
        }

        return $this->transactions()->create($data);
    }

    public function getDailySummary()
    {
        return [
            'opening_balance' => $this->opening_balance,
            'current_balance' => $this->current_balance,
            'total_sales' => $this->total_sales,
            'total_purchases' => $this->total_purchases,
            'total_income' => $this->total_income,
            'total_expense' => $this->total_expense,
            'transaction_count' => $this->transactions()->count(),
            'sales_count' => $this->transactions()->where('type', CashTransaction::TYPE_SALE)->count(),
            'expense_count' => $this->transactions()->whereIn('type', [
                CashTransaction::TYPE_EXPENSE,
                CashTransaction::TYPE_PURCHASE
            ])->count(),
            'opening_date' => $this->opening_date,
            'last_transaction' => $this->transactions()->latest()->first()?->created_at,
        ];
    }

    public function getTransactionHistory(array $filters = [])
    {
        return $this->transactions()
            ->with(['user', 'reference'])
            ->when(isset($filters['type']), function ($query) use ($filters) {
                $query->where('type', $filters['type']);
            })
            ->when(isset($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            })
            ->when(isset($filters['user_id']), function ($query) use ($filters) {
                $query->where('user_id', $filters['user_id']);
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }
}