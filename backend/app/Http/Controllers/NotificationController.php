<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get user's notifications with pagination and filters
     */
    public function index(Request $request)
    {
        $query = Notification::query()
            ->where(function ($query) {
                $query->where('notifiable_id', Auth::id())
                    ->where('notifiable_type', get_class(Auth::user()))
                    ->orWhere(function ($query) {
                        $query->whereNull('notifiable_id')
                            ->where('is_broadcast', true)
                            ->whereIn('store_id', Auth::user()->stores->pluck('id'));
                    });
            })
            ->when($request->read === 'true', function ($query) {
                $query->whereNotNull('read_at');
            })
            ->when($request->read === 'false', function ($query) {
                $query->whereNull('read_at');
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->from_date, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->to_date, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            });

        $notifications = $query->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json($notifications);
    }

    /**
     * Get notification statistics for the current user
     */
    public function stats()
    {
        $user = Auth::user();
        $baseQuery = Notification::where(function ($query) use ($user) {
            $query->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->orWhere(function ($query) use ($user) {
                    $query->whereNull('notifiable_id')
                        ->where('is_broadcast', true)
                        ->whereIn('store_id', $user->stores->pluck('id'));
                });
        });

        $stats = [
            'total_unread' => (clone $baseQuery)->whereNull('read_at')->count(),
            'total' => (clone $baseQuery)->count(),
            'by_priority' => [
                'high' => (clone $baseQuery)->where('priority', 'high')->whereNull('read_at')->count(),
                'medium' => (clone $baseQuery)->where('priority', 'medium')->whereNull('read_at')->count(),
                'low' => (clone $baseQuery)->where('priority', 'low')->whereNull('read_at')->count(),
            ],
            'by_category' => [
                'system' => (clone $baseQuery)->where('category', 'system')->whereNull('read_at')->count(),
                'stock' => (clone $baseQuery)->where('category', 'stock')->whereNull('read_at')->count(),
                'sales' => (clone $baseQuery)->where('category', 'sales')->whereNull('read_at')->count(),
                'purchase' => (clone $baseQuery)->where('category', 'purchase')->whereNull('read_at')->count(),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->update(['read_at' => Carbon::now()]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        Notification::where(function ($query) use ($user) {
            $query->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->orWhere(function ($query) use ($user) {
                    $query->whereNull('notifiable_id')
                        ->where('is_broadcast', true)
                        ->whereIn('store_id', $user->stores->pluck('id'));
                });
        })
        ->whereNull('read_at')
        ->update(['read_at' => Carbon::now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Clear all read notifications
     */
    public function clearAll(Request $request)
    {
        $user = Auth::user();
        
        Notification::where(function ($query) use ($user) {
            $query->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->orWhere(function ($query) use ($user) {
                    $query->whereNull('notifiable_id')
                        ->where('is_broadcast', true)
                        ->whereIn('store_id', $user->stores->pluck('id'));
                });
        })
        ->whereNotNull('read_at')
        ->delete();

        return response()->json(['message' => 'All read notifications cleared']);
    }

    /**
     * Get notification settings
     */
    public function getSettings(Request $request)
    {
        $user = Auth::user();
        $storeId = $request->store_id;

        $settings = [
            'global' => NotificationSetting::where('store_id', $storeId)->get(),
            'products' => ProductNotificationSetting::where('store_id', $storeId)
                ->with('product:id,name,sku')
                ->get(),
        ];

        return response()->json($settings);
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required|string',
            'settings.*.is_enabled' => 'boolean',
        ]);

        foreach ($request->settings as $setting) {
            NotificationSetting::updateOrCreate(
                [
                    'store_id' => $request->store_id,
                    'key' => $setting['key'],
                ],
                [
                    'value' => $setting['value'],
                    'is_enabled' => $setting['is_enabled'] ?? true,
                ]
            );
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    /**
     * Update product notification settings
     */
    public function updateProductSettings(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'required|exists:products,id',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'expiry_warning_days' => 'nullable|integer|min:0',
            'notify_on_low_stock' => 'boolean',
            'notify_on_expiry' => 'boolean',
        ]);

        ProductNotificationSetting::updateOrCreate(
            [
                'store_id' => $request->store_id,
                'product_id' => $request->product_id,
            ],
            [
                'low_stock_threshold' => $request->low_stock_threshold,
                'expiry_warning_days' => $request->expiry_warning_days,
                'notify_on_low_stock' => $request->notify_on_low_stock,
                'notify_on_expiry' => $request->notify_on_expiry,
            ]
        );

        return response()->json(['message' => 'Product settings updated successfully']);
    }
}