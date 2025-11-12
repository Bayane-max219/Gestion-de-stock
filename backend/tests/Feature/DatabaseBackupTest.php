<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class DatabaseBackupTest extends TestCase
{
    /** @test */
    public function it_creates_backup_file()
    {
        Storage::fake('local');

        Artisan::call('db:backup');

        $files = Storage::files('backups');
        
        $this->assertCount(1, $files);
        $this->assertMatchesRegularExpression(
            '/backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.zip/',
            $files[0]
        );
    }

    /** @test */
    public function it_cleans_old_backups()
    {
        Storage::fake('local');

        // Create some old backup files
        $oldDate = Carbon::now()->subDays(31)->format('Y-m-d_H-i-s');
        $newDate = Carbon::now()->format('Y-m-d_H-i-s');

        Storage::put("backups/backup_{$oldDate}.zip", 'old backup');
        Storage::put("backups/backup_{$newDate}.zip", 'new backup');

        Artisan::call('db:backup', ['--clean' => true]);

        $files = Storage::files('backups');
        
        $this->assertCount(1, $files);
        $this->assertStringContainsString($newDate, $files[0]);
    }
}