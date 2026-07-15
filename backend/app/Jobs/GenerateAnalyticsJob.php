<?php

namespace App\Jobs;

use App\Models\Analytics;
use App\Models\User;
use App\Models\Message;
use App\Models\Emergency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $backoff = [60, 300];
    public $timeout = 600; // 10 minutes

    /**
     * Create a new job instance.
     *
     * @param string $reportType
     * @param string $startDate
     * @param string $endDate
     * @param array $options
     */
    public function __construct(
        public string $reportType,
        public string $startDate,
        public string $endDate,
        public array $options = []
    ) {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $reportData = $this->generateReportData();

            // Create analytics report
            $analytics = Analytics::create([
                'report_type' => $this->reportType,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'data' => $reportData,
                'generated_at' => now(),
                'metadata' => $this->options,
            ]);

            Log::info('Analytics report generated successfully', [
                'report_type' => $this->reportType,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'analytics_id' => $analytics->id,
                'data_points' => count($reportData),
            ]);

        } catch (\Exception $e) {
            Log::error('Analytics report generation failed: ' . $e->getMessage(), [
                'report_type' => $this->reportType,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'exception' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate report data based on type.
     */
    private function generateReportData(): array
    {
        switch ($this->reportType) {
            case 'user_activity':
                return $this->generateUserActivityReport();
            
            case 'message_analytics':
                return $this->generateMessageAnalyticsReport();
            
            case 'emergency_stats':
                return $this->generateEmergencyStatsReport();
            
            case 'accessibility_usage':
                return $this->generateAccessibilityUsageReport();
            
            case 'system_overview':
                return $this->generateSystemOverviewReport();
            
            default:
                return [];
        }
    }

    /**
     * Generate user activity report.
     */
    private function generateUserActivityReport(): array
    {
        return [
            'total_users' => User::count(),
            'active_users_today' => User::whereDate('last_login_at', today())->count(),
            'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'users_by_accessibility_type' => DB::table('profiles')
                ->selectRaw('blind, deaf, mute, COUNT(*) as count')
                ->groupBy('blind', 'deaf', 'mute')
                ->get(),
        ];
    }

    /**
     * Generate message analytics report.
     */
    private function generateMessageAnalyticsReport(): array
    {
        return [
            'total_messages' => Message::count(),
            'messages_by_type' => DB::table('messages')
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'messages_by_date' => DB::table('messages')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'average_messages_per_day' => Message::whereBetween('created_at', [$this->startDate, $this->endDate])
                ->count() / max(1, now()->diffInDays($this->endDate)),
        ];
    }

    /**
     * Generate emergency statistics report.
     */
    private function generateEmergencyStatsReport(): array
    {
        return [
            'total_emergencies' => Emergency::count(),
            'emergencies_by_type' => DB::table('emergencies')
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'emergencies_by_date' => DB::table('emergencies')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'average_resolution_time' => $this->calculateAverageResolutionTime(),
        ];
    }

    /**
     * Generate accessibility usage report.
     */
    private function generateAccessibilityUsageReport(): array
    {
        return [
            'accessibility_features_usage' => DB::table('accessibility_logs')
                ->selectRaw('feature, COUNT(*) as usage_count')
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->groupBy('feature')
                ->get(),
            'accessibility_success_rate' => DB::table('accessibility_logs')
                ->selectRaw('AVG(CASE WHEN success = 1 THEN 1 ELSE 0 END) * 100 as success_rate')
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->first(),
            'accessibility_by_device_type' => DB::table('accessibility_logs')
                ->selectRaw('device_type, COUNT(*) as count')
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->groupBy('device_type')
                ->get(),
        ];
    }

    /**
     * Generate system overview report.
     */
    private function generateSystemOverviewReport(): array
    {
        return [
            'system_health' => [
                'total_storage_used' => $this->calculateStorageUsage(),
                'active_jobs_count' => DB::table('jobs')->where('attempts', '>', 0)->count(),
                'failed_jobs_today' => DB::table('failed_jobs')->whereDate('failed_at', today())->count(),
            ],
            'performance_metrics' => [
                'average_response_time' => $this->calculateAverageResponseTime(),
                'peak_usage_hours' => $this->calculatePeakUsageHours(),
                'system_uptime' => $this->calculateSystemUptime(),
            ],
        ];
    }

    /**
     * Calculate average emergency resolution time.
     */
    private function calculateAverageResolutionTime(): float
    {
        return Emergency::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, resolved_at, created_at)) as avg_resolution_hours')
            ->first()
            ->avg_resolution_hours ?? 0;
    }

    /**
     * Calculate storage usage.
     */
    private function calculateStorageUsage(): array
    {
        // Simulate storage calculation
        return [
            'total_size' => '2.5 GB',
            'used_size' => '1.8 GB',
            'available_size' => '0.7 GB',
            'usage_percentage' => 72,
        ];
    }

    /**
     * Calculate peak usage hours.
     */
    private function calculatePeakUsageHours(): array
    {
        // Simulate peak hours calculation
        return [
            'peak_hour' => 14,
            'peak_day' => 'Tuesday',
            'peak_week' => '2026-W20',
        ];
    }

    /**
     * Calculate system uptime.
     */
    private function calculateSystemUptime(): float
    {
        // Simulate uptime calculation
        return 99.8; // percentage
    }

    /**
     * Calculate average response time.
     */
    private function calculateAverageResponseTime(): float
    {
        // Simulate response time calculation
        return 245; // milliseconds
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Analytics report generation job failed', [
            'report_type' => $this->reportType,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * The job has finished processing.
     */
    public function success(): void
    {
        Log::info('Analytics report generation job completed successfully', [
            'report_type' => $this->reportType,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'completed_at' => now(),
        ]);
    }
}
