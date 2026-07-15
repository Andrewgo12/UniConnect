<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Message;
use App\Models\Emergency;
use App\Models\MedicalRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $backoff = [300, 900];
    public $timeout = 1800; // 30 minutes

    /**
     * Create a new job instance.
     *
     * @param string $backupType
     * @param array $options
     */
    public function __construct(
        public string $backupType = 'full',
        public array $options = []
    ) {
        $this->backupType = $backupType;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupPath = "backups/{$this->backupType}_{$timestamp}";

            Log::info('Starting data backup', [
                'backup_type' => $this->backupType,
                'timestamp' => $timestamp,
            ]);

            // Create backup data based on type
            $backupData = $this->generateBackupData();

            // Store backup file
            $filename = "{$backupPath}.json";
            Storage::disk('backups')->put($filename, json_encode($backupData, JSON_PRETTY_PRINT));

            // Create database backup if requested
            if ($this->options['include_database'] ?? false) {
                $this->createDatabaseBackup($backupPath);
            }

            // Clean old backups
            $this->cleanupOldBackups();

            Log::info('Data backup completed successfully', [
                'backup_type' => $this->backupType,
                'timestamp' => $timestamp,
                'file_size' => strlen(json_encode($backupData)),
                'filename' => $filename,
            ]);

        } catch (\Exception $e) {
            Log::error('Data backup failed: ' . $e->getMessage(), [
                'backup_type' => $this->backupType,
                'timestamp' => now(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate backup data based on type.
     */
    private function generateBackupData(): array
    {
        switch ($this->backupType) {
            case 'users':
                return $this->backupUsers();
            case 'messages':
                return $this->backupMessages();
            case 'emergencies':
                return $this->backupEmergencies();
            case 'medical':
                return $this->backupMedicalRecords();
            case 'analytics':
                return $this->backupAnalytics();
            default:
                return $this->backupFull();
        }
    }

    /**
     * Backup users data.
     */
    private function backupUsers(): array
    {
        return [
            'type' => 'users',
            'backup_date' => now()->toISOString(),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'users' => User::all(['id', 'name', 'email', 'accessibility_needs', 'created_at', 'updated_at'])->toArray(),
        ];
    }

    /**
     * Backup messages data.
     */
    private function backupMessages(): array
    {
        return [
            'type' => 'messages',
            'backup_date' => now()->toISOString(),
            'total_messages' => Message::count(),
            'messages' => Message::with('user')->limit(1000)->get(['id', 'content', 'type', 'user_id', 'created_at'])->toArray(),
        ];
    }

    /**
     * Backup emergencies data.
     */
    private function backupEmergencies(): array
    {
        return [
            'type' => 'emergencies',
            'backup_date' => now()->toISOString(),
            'total_emergencies' => Emergency::count(),
            'emergencies' => Emergency::with('user')->limit(1000)->get(['id', 'type', 'severity', 'user_id', 'created_at'])->toArray(),
        ];
    }

    /**
     * Backup medical records data.
     */
    private function backupMedicalRecords(): array
    {
        return [
            'type' => 'medical',
            'backup_date' => now()->toISOString(),
            'total_records' => MedicalRecord::count(),
            'records' => MedicalRecord::with('user')->limit(1000)->get(['id', 'title', 'type', 'user_id', 'created_at'])->toArray(),
        ];
    }

    /**
     * Backup analytics data.
     */
    private function backupAnalytics(): array
    {
        return [
            'type' => 'analytics',
            'backup_date' => now()->toISOString(),
            'analytics_summary' => [
                'total_events' => DB::table('analytics')->count(),
                'events_last_30_days' => DB::table('analytics')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
            ],
        ];
    }

    /**
     * Backup all data.
     */
    private function backupFull(): array
    {
        return [
            'type' => 'full',
            'backup_date' => now()->toISOString(),
            'users' => $this->backupUsers(),
            'messages' => $this->backupMessages(),
            'emergencies' => $this->backupEmergencies(),
            'medical' => $this->backupMedicalRecords(),
            'analytics' => $this->backupAnalytics(),
        ];
    }

    /**
     * Create database backup.
     */
    private function createDatabaseBackup(string $backupPath): void
    {
        try {
            $dbPath = database_path('database.sqlite');
            $backupDbPath = "{$backupPath}_database.sqlite";
            
            if (file_exists($dbPath)) {
                copy($dbPath, $backupDbPath);
                
                Log::info('Database backup created', [
                    'backup_path' => $backupDbPath,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Database backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Clean up old backup files.
     */
    private function cleanupOldBackups(): void
    {
        try {
            $files = Storage::disk('backups')->allFiles();
            $keepCount = 10; // Keep last 10 backups
            
            if (count($files) > $keepCount) {
                usort($files, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                $filesToDelete = array_slice($files, 0, count($files) - $keepCount);
                
                foreach ($filesToDelete as $file) {
                    Storage::disk('backups')->delete($file);
                }
                
                Log::info('Old backups cleaned up', [
                    'deleted_count' => count($filesToDelete),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Backup cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Data backup job failed', [
            'backup_type' => $this->backupType,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * The job has finished processing.
     */
    public function success(): void
    {
        Log::info('Data backup job completed successfully', [
            'backup_type' => $this->backupType,
            'completed_at' => now(),
        ]);
    }
}
