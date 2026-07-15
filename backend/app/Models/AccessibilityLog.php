<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class AccessibilityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loggable_type',
        'loggable_id',
        'action', // screen_reader_used, voice_command, sign_language_requested, high_contrast_enabled, etc.
        'feature', // text_to_speech, voice_recognition, sign_language, high_contrast, large_text, etc.
        'accessibility_mode', // standard, screen_reader, voice_control, sign_language, high_contrast, large_text
        'device_type', // mobile, desktop, tablet, voice_assistant
        'input_method', // touch, keyboard, voice, gesture, eye_tracking
        'assistive_technology', // screen_reader, voice_recognizer, sign_language_interpreter, braille_display
        'duration', // time spent using accessibility feature in seconds
        'success',
        'error_message',
        'context',
        'previous_mode',
        'new_mode',
        'metadata',
        'platform',
        'response_time',
        'language',
        'location',
        'timezone',
        'error_code',
    ];

    protected $casts = [
        'success' => 'boolean',
        'duration' => 'integer',
        'response_time' => 'integer',
        'context' => 'json',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByFeature($query, string $feature)
    {
        return $query->where('feature', $feature);
    }

    public function scopeByMode($query, string $mode)
    {
        return $query->where('accessibility_mode', $mode);
    }

    public function scopeByDevice($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public function scopeByInputMethod($query, string $inputMethod)
    {
        return $query->where('input_method', $inputMethod);
    }

    public function scopeByAssistiveTechnology($query, string $technology)
    {
        return $query->where('assistive_technology', $technology);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
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

    public static function logAccessibilityAction(User $user, string $action, array $additionalData = []): self
    {
        return self::create(array_merge([
            'user_id' => $user->id,
            'action' => $action,
            'success' => true,
            'device_type' => self::detectDeviceType(),
            'input_method' => self::detectInputMethod(),
        ], $additionalData));
    }

    public static function logScreenReaderUsage(User $user, string $content, bool $success = true, string $errorMessage = null): self
    {
        return self::logAccessibilityAction($user, 'screen_reader_used', [
            'feature' => 'text_to_speech',
            'accessibility_mode' => 'screen_reader',
            'assistive_technology' => 'screen_reader',
            'context' => ['content_length' => strlen($content), 'content_preview' => substr($content, 0, 100)],
            'success' => $success,
            'error_message' => $errorMessage,
        ]);
    }

    public static function logVoiceCommand(User $user, string $command, bool $success = true, string $errorMessage = null): self
    {
        return self::logAccessibilityAction($user, 'voice_command_executed', [
            'feature' => 'voice_recognition',
            'accessibility_mode' => 'voice_control',
            'assistive_technology' => 'voice_recognizer',
            'context' => ['command' => $command],
            'success' => $success,
            'error_message' => $errorMessage,
        ]);
    }

    public static function logSignLanguageRequest(User $user, string $content, bool $success = true, string $errorMessage = null): self
    {
        return self::logAccessibilityAction($user, 'sign_language_requested', [
            'feature' => 'sign_language',
            'accessibility_mode' => 'sign_language',
            'assistive_technology' => 'sign_language_interpreter',
            'context' => ['content_length' => strlen($content)],
            'success' => $success,
            'error_message' => $errorMessage,
        ]);
    }

    public static function logHighContrastToggle(User $user, bool $enabled): self
    {
        return self::logAccessibilityAction($user, 'high_contrast_toggled', [
            'feature' => 'high_contrast',
            'accessibility_mode' => $enabled ? 'high_contrast' : 'standard',
            'context' => ['enabled' => $enabled],
        ]);
    }

    public static function logLargeTextAdjustment(User $user, int $fontSize): self
    {
        return self::logAccessibilityAction($user, 'large_text_adjusted', [
            'feature' => 'large_text',
            'accessibility_mode' => 'large_text',
            'context' => ['font_size' => $fontSize],
        ]);
    }

    public static function logModeChange(User $user, string $previousMode, string $newMode): self
    {
        return self::logAccessibilityAction($user, 'accessibility_mode_changed', [
            'previous_mode' => $previousMode,
            'new_mode' => $newMode,
            'context' => ['change_reason' => 'user_initiated'],
        ]);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDeviceType($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public function scopeByDate($query, string $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public static function logFeatureUse(array $data): self
    {
        return self::create(array_merge([
            'success' => true,
            'device_type' => 'mobile',
            'input_method' => 'touch',
            'assistive_technology' => 'screen_reader',
            'duration' => 0,
        ], $data));
    }

    public static function logAccessibilityError(array $data): self
    {
        return self::create(array_merge([
            'success' => false,
            'device_type' => 'mobile',
            'input_method' => 'touch',
            'assistive_technology' => 'screen_reader',
            'duration' => 0,
        ], $data));
    }

    public static function getUsageStatistics(int $userId): array
    {
        $total = self::where('user_id', $userId)->count();
        $successful = self::where('user_id', $userId)->where('success', true)->count();

        return [
            'total_logs' => $total,
            'successful_logs' => $successful,
            'success_rate' => $total > 0 ? ($successful / $total) * 100 : 0.0,
            'most_used_features' => self::where('user_id', $userId)
                ->selectRaw('feature, COUNT(*) as cnt')
                ->groupBy('feature')
                ->orderByDesc('cnt')
                ->limit(5)
                ->pluck('cnt', 'feature')
                ->toArray(),
            'average_response_time' => (float) (self::where('user_id', $userId)->avg('response_time') ?? 0),
        ];
    }

    /**
     * @return array{feature: string, period: string, data: array<int, array<string, mixed>>}
     */
    public static function getFeatureUsageByPeriod(string $feature, \DateTimeInterface $since): array
    {
        return [
            'feature' => $feature,
            'period' => '30_days',
            'data' => self::where('feature', $feature)
                ->where('created_at', '>=', $since)
                ->orderBy('created_at')
                ->get(['id', 'created_at', 'success'])
                ->toArray(),
        ];
    }

    /**
     * @return array{device_type: string, period: string, data: array<int, array<string, mixed>>}
     */
    public static function getDeviceUsageByPeriod(string $deviceType, \DateTimeInterface $since): array
    {
        return [
            'device_type' => $deviceType,
            'period' => '30_days',
            'data' => self::where('device_type', $deviceType)
                ->where('created_at', '>=', $since)
                ->orderBy('created_at')
                ->get(['id', 'created_at', 'success'])
                ->toArray(),
        ];
    }

    public static function getSuccessRate(int $userId): float
    {
        $total = self::where('user_id', $userId)->count();
        if ($total === 0) {
            return 0.0;
        }

        $successful = self::where('user_id', $userId)->where('success', true)->count();

        return ($successful / $total) * 100;
    }

    /**
     * @return array<string, int>
     */
    public static function getMostUsedFeatures(int $userId): array
    {
        $counts = self::where('user_id', $userId)
            ->selectRaw('feature, COUNT(*) as cnt')
            ->groupBy('feature')
            ->pluck('cnt', 'feature')
            ->toArray();

        return [
            'screen_reader' => (int) ($counts['screen_reader'] ?? 0),
            'voice_commands' => (int) ($counts['voice_commands'] ?? 0),
        ];
    }

    public static function getUsageStats(\DateTime $startDate, \DateTime $endDate): array
    {
        $logs = self::inDateRange($startDate, $endDate);

        return [
            'total_interactions' => $logs->count(),
            'unique_users' => $logs->distinct('user_id')->count('user_id'),
            'success_rate' => $logs->where('success', true)->count() / max(1, $logs->count()) * 100,
            'most_used_features' => $logs->selectRaw('feature, COUNT(*) as count')
                                   ->groupBy('feature')
                                   ->orderByDesc('count')
                                   ->limit(5)
                                   ->pluck('count', 'feature')
                                   ->toArray(),
            'most_used_modes' => $logs->selectRaw('accessibility_mode, COUNT(*) as count')
                                  ->groupBy('accessibility_mode')
                                  ->orderByDesc('count')
                                  ->limit(5)
                                  ->pluck('count', 'accessibility_mode')
                                  ->toArray(),
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

    public static function getAssistiveTechnologyStats(\DateTime $startDate, \DateTime $endDate): array
    {
        return self::inDateRange($startDate, $endDate)
                   ->selectRaw('assistive_technology, COUNT(*) as count')
                   ->groupBy('assistive_technology')
                   ->orderByDesc('count')
                   ->pluck('count', 'assistive_technology')
                   ->toArray();
    }

    private static function detectDeviceType(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return preg_match('/iPad/', $userAgent) ? 'tablet' : 'mobile';
        } elseif (preg_match('/Tablet/', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private static function detectInputMethod(): string
    {
        // This would typically be determined by the client-side application
        // For now, we'll use a basic heuristic based on user agent
        $userAgent = request()->userAgent();
        
        if (preg_match('/Mobile|Android/', $userAgent)) {
            return 'touch';
        } elseif (preg_match('/Voice/', $userAgent)) {
            return 'voice';
        }
        
        return 'keyboard';
    }
}
