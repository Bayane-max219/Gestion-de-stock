<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--clean : Remove old backups}';
    protected $description = 'Backup the database';

    protected $keepBackups = 30; // Number of days to keep backups

    public function handle()
    {
        $this->info('Starting database backup...');

        // Create backups directory if it doesn't exist
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        // Clean old backups if requested
        if ($this->option('clean')) {
            $this->cleanOldBackups();
        }

        // Generate backup filename
        $filename = sprintf(
            'backup_%s.sql',
            Carbon::now()->format('Y-m-d_H-i-s')
        );

        // Get database configuration
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // Build mysqldump command
        $command = [
            'mysqldump',
            '--host=' . $host,
            '--user=' . $username,
            '--password=' . $password,
            $database,
            '--result-file=' . storage_path('app/backups/' . $filename),
            '--single-transaction',
            '--skip-lock-tables'
        ];

        // Execute backup
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Backup failed: ' . $process->getErrorOutput());
            return 1;
        }

        // Compress backup
        $zipFilename = str_replace('.sql', '.zip', $filename);
        $zip = new \ZipArchive();
        $zip->open(storage_path('app/backups/' . $zipFilename), \ZipArchive::CREATE);
        $zip->addFile(storage_path('app/backups/' . $filename), $filename);
        $zip->close();

        // Remove uncompressed file
        unlink(storage_path('app/backups/' . $filename));

        $this->info('Backup completed successfully: ' . $zipFilename);
        return 0;
    }

    protected function cleanOldBackups()
    {
        $this->info('Cleaning old backups...');
        
        $files = Storage::files('backups');
        $now = Carbon::now();

        foreach ($files as $file) {
            $pattern = '/backup_(\d{4}-\d{2}-\d{2})_/';
            if (preg_match($pattern, $file, $matches)) {
                $backupDate = Carbon::createFromFormat('Y-m-d', $matches[1]);
                
                if ($backupDate->diffInDays($now) > $this->keepBackups) {
                    Storage::delete($file);
                    $this->line('Deleted old backup: ' . $file);
                }
            }
        }
    }
}