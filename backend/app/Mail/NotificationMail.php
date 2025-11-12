<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function build()
    {
        return $this->markdown('emails.notification')
            ->subject($this->getSubject())
            ->with([
                'notification' => $this->notification,
                'data' => $this->notification->data,
                'actionUrl' => $this->getActionUrl(),
            ]);
    }

    protected function getSubject()
    {
        $prefix = config('app.name') . ' - ';

        switch ($this->notification->type) {
            case 'low_stock':
                return $prefix . 'Low Stock Alert';
            case 'expiring_product':
                return $prefix . 'Product Expiration Alert';
            case 'daily_summary':
                return $prefix . 'Daily Summary Report';
            case 'weekly_summary':
                return $prefix . 'Weekly Summary Report';
            case 'monthly_summary':
                return $prefix . 'Monthly Summary Report';
            default:
                return $prefix . 'New Notification';
        }
    }

    protected function getActionUrl()
    {
        $baseUrl = config('app.url');
        $type = $this->notification->type;

        switch ($type) {
            case 'low_stock':
                return $baseUrl . '/inventory/products/' . ($this->notification->data['product_id'] ?? '');
            case 'expiring_product':
                return $baseUrl . '/inventory/products/' . ($this->notification->data['product_id'] ?? '');
            case 'daily_summary':
            case 'weekly_summary':
            case 'monthly_summary':
                return $baseUrl . '/reports/summary';
            default:
                return $baseUrl . '/notifications';
        }
    }
}