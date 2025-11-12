<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\ProductNotificationSetting;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Create a new notification
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $notification = new Notification([
                'type' => $data['type'],
                'data' => $data['data'],
                'category' => $data['category'] ?? 'system',
                'priority' => $data['priority'] ?? 'medium',
                'store_id' => $data['store_id'] ?? null,
                'is_broadcast' => $data['is_broadcast'] ?? false,
                'expires_at' => $data['expires_at'] ?? null,
                'created_by' => auth()->id(),
            ]);

            if (isset($data['notifiable_type']) && isset($data['notifiable_id'])) {
                $notification->notifiable_type = $data['notifiable_type'];
                $notification->notifiable_id = $data['notifiable_id'];
            }

            $notification->save();

            // Process notification delivery
            $this->processNotification($notification);

            return $notification;
        });
    }

    /**
     * Process notification delivery through different channels
     */
    protected function processNotification(Notification $notification)
    {
        $channels = ['database'];

        // Add email channel if notification is important
        if ($notification->priority === 'high') {
            $channels[] = 'email';
        }

        // Add broadcast channel if it's a real-time notification
        if ($notification->is_broadcast) {
            $channels[] = 'broadcast';
        }

        foreach ($channels as $channel) {
            try {
                $this->{"send" . ucfirst($channel) . "Notification"}($notification);
                
                $this->logNotification($notification, $channel, 'sent');
            } catch (\Exception $e) {
                $this->logNotification($notification, $channel, 'failed', $e->getMessage());
            }
        }
    }

    /**
     * Send database notification
     */
    protected function sendDatabaseNotification(Notification $notification)
    {
        // Database notification is already created
        return true;
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification(Notification $notification)
    {
        $recipients = $this->getNotificationRecipients($notification);

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new \App\Mail\NotificationMail($notification));
        }
    }

    /**
     * Send broadcast notification
     */
    protected function sendBroadcastNotification(Notification $notification)
    {
        broadcast(new \App\Events\NewNotification($notification))->toOthers();
    }

    /**
     * Log notification delivery attempt
     */
    protected function logNotification(Notification $notification, string $channel, string $status, ?string $error = null)
    {
        $notification->logs()->create([
            'channel' => $channel,
            'status' => $status,
            'error_message' => $error,
            'processed_at' => now(),
        ]);
    }

    /**
     * Get notification recipients based on type and settings
     */
    protected function getNotificationRecipients(Notification $notification)
    {
        if ($notification->notifiable_type === User::class) {
            return [User::find($notification->notifiable_id)];
        }

        $query = User::query();

        if ($notification->store_id) {
            $query->whereHas('stores', function ($q) use ($notification) {
                $q->where('stores.id', $notification->store_id);
            });
        }

        switch ($notification->category) {
            case 'stock':
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'magasinier']);
                });
                break;
            case 'sales':
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'commercial']);
                });
                break;
            default:
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                });
        }

        return $query->get();
    }

    /**
     * Check for low stock products and create notifications
     */
    public function checkLowStock()
    {
        $products = Product::with(['storeProducts', 'notificationSettings'])
            ->whereHas('storeProducts', function ($query) {
                $query->whereRaw('quantity <= COALESCE(product_notification_settings.low_stock_threshold, notification_settings.value::integer, products.min_quantity)')
                    ->join('product_notification_settings', function ($join) {
                        $join->on('store_products.product_id', '=', 'product_notification_settings.product_id')
                            ->on('store_products.store_id', '=', 'product_notification_settings.store_id');
                    })
                    ->leftJoin('notification_settings', function ($join) {
                        $join->on('store_products.store_id', '=', 'notification_settings.store_id')
                            ->where('notification_settings.key', 'low_stock_threshold');
                    });
            })
            ->get();

        foreach ($products as $product) {
            foreach ($product->storeProducts as $storeProduct) {
                if ($this->shouldNotifyLowStock($product, $storeProduct)) {
                    $this->create([
                        'type' => 'low_stock',
                        'category' => 'stock',
                        'priority' => 'high',
                        'store_id' => $storeProduct->store_id,
                        'data' => [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'current_quantity' => $storeProduct->quantity,
                            'min_quantity' => $product->min_quantity,
                        ],
                    ]);
                }
            }
        }
    }

    /**
     * Check for products nearing expiration
     */
    public function checkExpiringProducts()
    {
        $products = Product::with(['storeProducts', 'notificationSettings'])
            ->whereNotNull('expiry_date')
            ->whereHas('storeProducts', function ($query) {
                $query->whereRaw('products.expiry_date <= CURRENT_DATE + COALESCE(product_notification_settings.expiry_warning_days, notification_settings.value::integer, 30) * INTERVAL \'1 day\'')
                    ->join('product_notification_settings', function ($join) {
                        $join->on('store_products.product_id', '=', 'product_notification_settings.product_id')
                            ->on('store_products.store_id', '=', 'product_notification_settings.store_id');
                    })
                    ->leftJoin('notification_settings', function ($join) {
                        $join->on('store_products.store_id', '=', 'notification_settings.store_id')
                            ->where('notification_settings.key', 'expiry_warning_days');
                    });
            })
            ->get();

        foreach ($products as $product) {
            foreach ($product->storeProducts as $storeProduct) {
                if ($this->shouldNotifyExpiry($product, $storeProduct)) {
                    $this->create([
                        'type' => 'expiring_product',
                        'category' => 'stock',
                        'priority' => 'high',
                        'store_id' => $storeProduct->store_id,
                        'data' => [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'expiry_date' => $product->expiry_date,
                            'current_quantity' => $storeProduct->quantity,
                        ],
                    ]);
                }
            }
        }
    }

    /**
     * Generate and send daily summary notifications
     */
    public function sendDailySummary()
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            $summary = $this->generateStoreSummary($store, 'daily');
            
            $this->create([
                'type' => 'daily_summary',
                'category' => 'system',
                'priority' => 'medium',
                'store_id' => $store->id,
                'data' => $summary,
                'is_broadcast' => true,
            ]);
        }
    }

    /**
     * Generate store summary data
     */
    protected function generateStoreSummary(Store $store, string $period)
    {
        $dateRange = $this->getDateRangeForPeriod($period);

        return [
            'period' => $period,
            'date_range' => $dateRange,
            'sales' => [
                'count' => $store->sales()->whereBetween('created_at', $dateRange)->count(),
                'total' => $store->sales()->whereBetween('created_at', $dateRange)->sum('total_amount'),
            ],
            'purchases' => [
                'count' => $store->purchases()->whereBetween('created_at', $dateRange)->count(),
                'total' => $store->purchases()->whereBetween('created_at', $dateRange)->sum('total_amount'),
            ],
            'stock_movements' => $store->stockMovements()->whereBetween('created_at', $dateRange)->count(),
            'low_stock_items' => $store->products()
                ->whereHas('storeProducts', function ($query) {
                    $query->whereRaw('quantity <= min_quantity');
                })->count(),
        ];
    }

    /**
     * Get date range for summary periods
     */
    protected function getDateRangeForPeriod(string $period): array
    {
        $now = Carbon::now();

        switch ($period) {
            case 'daily':
                return [$now->startOfDay(), $now->endOfDay()];
            case 'weekly':
                return [$now->startOfWeek(), $now->endOfWeek()];
            case 'monthly':
                return [$now->startOfMonth(), $now->endOfMonth()];
            default:
                return [$now->startOfDay(), $now->endOfDay()];
        }
    }

    /**
     * Determine if we should send a low stock notification
     */
    protected function shouldNotifyLowStock(Product $product, $storeProduct): bool
    {
        $settings = $product->notificationSettings()
            ->where('store_id', $storeProduct->store_id)
            ->first();

        if (!$settings || $settings->notify_on_low_stock) {
            $threshold = $settings->low_stock_threshold ?? $product->min_quantity;
            return $storeProduct->quantity <= $threshold;
        }

        return false;
    }

    /**
     * Determine if we should send an expiry notification
     */
    protected function shouldNotifyExpiry(Product $product, $storeProduct): bool
    {
        $settings = $product->notificationSettings()
            ->where('store_id', $storeProduct->store_id)
            ->first();

        if (!$settings || $settings->notify_on_expiry) {
            $warningDays = $settings->expiry_warning_days ?? 30;
            return $product->expiry_date <= Carbon::now()->addDays($warningDays);
        }

        return false;
    }
}