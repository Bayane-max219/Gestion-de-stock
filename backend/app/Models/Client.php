<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, HasUuids;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_BLACKLISTED = 'blacklisted';

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'tax_number',
        'credit_limit',
        'payment_terms',
        'status',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'last_purchase_date' => 'datetime',
    ];

    protected $appends = [
        'total_sales',
        'total_paid',
        'total_due',
        'available_credit',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalSalesAttribute()
    {
        return $this->sales()->sum('total');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getTotalDueAttribute()
    {
        return $this->total_sales - $this->total_paid;
    }

    public function getAvailableCreditAttribute()
    {
        return $this->credit_limit - $this->total_due;
    }

    public function hasAvailableCredit(float $amount): bool
    {
        return $this->available_credit >= $amount;
    }

    public function generateClientCode()
    {
        $prefix = 'CL';
        $lastClient = self::where('code', 'like', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->first();

        if ($lastClient) {
            $sequence = (int) substr($lastClient->code, 2);
            $sequence++;
        } else {
            $sequence = 1;
        }

        $this->code = sprintf("%s%06d", $prefix, $sequence);
        return $this->code;
    }

    public function getOverdueSales()
    {
        return $this->sales()
            ->where('payment_status', '!=', Sale::STATUS_PAID)
            ->where('sale_date', '<=', now()->subDays($this->payment_terms))
            ->get();
    }

    public function getSalesHistory(array $filters = [])
    {
        return $this->sales()
            ->with(['items.product', 'payments'])
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('payment_status', $filters['status']);
            })
            ->when(isset($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('sale_date', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('sale_date', '<=', $filters['date_to']);
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getPaymentHistory(array $filters = [])
    {
        return $this->payments()
            ->with('sale')
            ->when(isset($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('payment_date', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('payment_date', '<=', $filters['date_to']);
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function updateStatus()
    {
        $overdueDays = $this->getOverdueSales()->count();
        
        if ($overdueDays > 90) {
            $this->status = self::STATUS_BLACKLISTED;
        } elseif ($overdueDays > 0) {
            // Keep current status but maybe send notification
        } else {
            $this->status = self::STATUS_ACTIVE;
        }

        $this->save();
    }
}