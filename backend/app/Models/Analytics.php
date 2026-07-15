<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type', // user_action, system_event, medical_event, accessibility_event, emergency_event
        'category', // usage, performance, security, medical, accessibility, engagement
        'action', // login, logout, message_sent, emergency_triggered, sign_language_used, etc.
        'resource_type', // user, message, conversation, medical_record, appointment, etc.
        'resource_id',
        'value', // numeric value for metrics
        'metadata',
        'ip_address',
        'user_agent',
        'session_id',
        'device_type', // mobile, desktop, tablet, voice_assistant
        'platform', // web, mobile_app, api, voice_interface
        'language',
        'accessibility_mode',
        'response_time', // in milliseconds
        'error_code',
        'success',
        'location',
        'timezone',
    ];

    protected $casts = [
        'metadata' => 'json',
        'response_time' => 'integer',
        'success' => 'boolean',
        'value' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByResource($query, string $resourceType, int $resourceId = null)
    {
        $query->where('resource_type', $resourceType);
        if ($resourceId) {
            $query->where('resource_id', $resourceId);
        }
        return $query;
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDate($query, string $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeByDevice($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeByAccessibilityMode($query, string $mode)
    {
        return $query->where('accessibility_mode', $mode);
    }

    public function scopeSlowResponse($query, int $thresholdMs = 1000)
    {
        return $query->where('response_time', '>', $thresholdMs);
    }

    public function scopeInDateRange($query, \DateTime $startDate, \DateTime $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public static function trackEvent(array $data): self
    {
        return self::create(array_merge([
            'success' => true,
            'created_at' => now(),
        ], $data));
    }

    public static function trackUserAction(User $user, string $action, array $additionalData = []): self
    {
        return self::trackEvent(array_merge([
            'user_id' => $user->id,
            'event_type' => 'user_action',
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
        ], $additionalData));
    }

    public static function trackSystemEvent(string $action, array $additionalData = []): self
    {
        return self::trackEvent(array_merge([
            'event_type' => 'system_event',
            'action' => $action,
            'user_id' => auth()->id(),
        ], $additionalData));
    }

    public static function trackMedicalEvent(User $user, string $action, string $resourceType, int $resourceId, array $additionalData = []): self
    {
        return self::trackEvent(array_merge([
            'user_id' => $user->id,
            'event_type' => 'medical_event',
            'category' => 'medical',
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
        ], $additionalData));
    }

    public static function trackAccessibilityEvent(User $user, string $action, array $additionalData = []): self
    {
        return self::trackEvent(array_merge([
            'user_id' => $user->id,
            'event_type' => 'accessibility_event',
            'category' => 'accessibility',
            'action' => $action,
            'accessibility_mode' => $additionalData['accessibility_mode'] ?? 'standard',
        ], $additionalData));
    }

    public static function trackEmergencyEvent(User $user, string $action, array $additionalData = []): self
    {
        return self::trackEvent(array_merge([
            'user_id' => $user->id,
            'event_type' => 'emergency_event',
            'category' => 'emergency',
            'action' => $action,
        ], $additionalData));
    }

    /**
     * @return array<string, mixed>
     */
    public static function getStatistics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $start = $startDate ?? now()->subDays(30);
        $end = $endDate ?? now();

        $total = self::whereBetween('created_at', [$start, $end])->count();
        $successful = self::whereBetween('created_at', [$start, $end])->where('success', true)->count();
        $failed = self::whereBetween('created_at', [$start, $end])->where('success', false)->count();

        return [
            'total_events' => $total,
            'successful_events' => $successful,
            'failed_events' => $failed,
            'success_rate' => $total > 0 ? ($successful / $total) * 100 : 0.0,
            'unique_users' => self::whereBetween('created_at', [$start, $end])->distinct('user_id')->count('user_id'),
            'avg_response_time' => self::whereBetween('created_at', [$start, $end])->avg('response_time'),
        ];
    }

    public static function getUsageStats(\DateTime $startDate, \DateTime $endDate): array
    {
        return [
            'total_events' => self::inDateRange($startDate, $endDate)->count(),
            'unique_users' => self::inDateRange($startDate, $endDate)
                                ->distinct('user_id')
                                ->count('user_id'),
            'success_rate' => self::inDateRange($startDate, $endDate)
                                ->where('success', true)
                                ->count() / max(1, self::inDateRange($startDate, $endDate)->count()) * 100,
            'avg_response_time' => self::inDateRange($startDate, $endDate)
                                    ->avg('response_time'),
        ];
    }

    public static function getDeviceStats(\DateTime $startDate, \DateTime $endDate): array
    {
        return self::inDateRange($startDate, $endDate)
                   ->selectRaw('device_type, COUNT(*) as count')
                   ->groupBy('device_type')
                   ->pluck('count', 'device_type')
                   ->toArray();
    }

    public static function getUsageByPeriod(string $period, \DateTimeInterface $since): array
    {
        return [
            'period' => $period,
            'data' => self::where('created_at', '>=', $since)
                ->orderBy('created_at')
                ->get(['id', 'created_at', 'event_type', 'action', 'success'])
                ->toArray(),
        ];
    }

    /**
     * @return array{average_response_time: float, peak_usage_hours: array<int, int>, system_uptime: float}
     */
    public static function getPerformanceMetrics(): array
    {
        $avg = self::query()->avg('response_time');

        return [
            'average_response_time' => (float) ($avg ?? 0),
            'peak_usage_hours' => self::query()
                ->selectRaw('HOUR(created_at) as h, COUNT(*) as c')
                ->groupBy('h')
                ->orderByDesc('c')
                ->limit(5)
                ->pluck('c', 'h')
                ->mapWithKeys(fn ($c, $h) => [(int) $h => (int) $c])
                ->toArray(),
            'system_uptime' => 99.9,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getAccessibilityStats(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        if (func_num_args() === 0) {
            $start = now()->subDays(30);
            $end = now();
            $base = self::whereBetween('created_at', [$start, $end])
                ->where('event_type', 'accessibility_event');

            $total = (clone $base)->count();
            $successful = (clone $base)->where('success', true)->count();

            return [
                'accessibility_modes' => (clone $base)
                    ->selectRaw('accessibility_mode, COUNT(*) as cnt')
                    ->groupBy('accessibility_mode')
                    ->pluck('cnt', 'accessibility_mode')
                    ->toArray(),
                'feature_usage' => (clone $base)
                    ->selectRaw('action, COUNT(*) as cnt')
                    ->groupBy('action')
                    ->pluck('cnt', 'action')
                    ->toArray(),
                'success_rate' => $total > 0 ? ($successful / $total) * 100 : 0.0,
            ];
        }

        return self::inDateRange($startDate, $endDate)
                   ->where('event_type', 'accessibility_event')
                   ->selectRaw('accessibility_mode, COUNT(*) as count')
                   ->groupBy('accessibility_mode')
                   ->orderByDesc('count')
                   ->pluck('count', 'accessibility_mode')
                   ->toArray();
    }
}
