<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class ProcessNotifications extends Command
{
    protected $signature = 'notifications:process {type=all}';
    protected $description = 'Process notifications based on type (low-stock, expiring, summary)';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $type = $this->argument('type');

        try {
            switch ($type) {
                case 'low-stock':
                    $this->info('Processing low stock notifications...');
                    $this->notificationService->checkLowStock();
                    break;

                case 'expiring':
                    $this->info('Processing expiring products notifications...');
                    $this->notificationService->checkExpiringProducts();
                    break;

                case 'daily-summary':
                    $this->info('Processing daily summary notifications...');
                    $this->notificationService->sendDailySummary();
                    break;

                case 'all':
                    $this->info('Processing all notifications...');
                    $this->notificationService->checkLowStock();
                    $this->notificationService->checkExpiringProducts();
                    $this->notificationService->sendDailySummary();
                    break;

                default:
                    $this->error('Invalid notification type specified');
                    return 1;
            }

            $this->info('Notification processing completed successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error processing notifications: ' . $e->getMessage());
            return 1;
        }
    }
}