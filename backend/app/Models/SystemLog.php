<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'level', // debug, info, notice, warning, error, critical, alert, emergency
        'message',
        'context',
        'channel', // app, database, mail, queue, auth, security, medical, accessibility
        'component', // controller, model, job, middleware, service, etc.
        'action', // specific action or method name
        'resource_type',
        'resource_id',
        'ip_address',
        'user_agent',
        'session_id',
        'request_id',
        'memory_usage',
        'execution_time',
        'stack_trace',
        'exception_class',
        'file',
        'line',
        'metadata',
    ];

    protected $casts = [
        'context' => 'json',
        'metadata' => 'json',
        'memory_usage' => 'integer',
        'execution_time' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeByComponent($query, string $component)
    {
        return $query->where('component', $component);
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

    public function scopeError($query)
    {
        return $query->whereIn('level', ['error', 'critical', 'alert', 'emergency']);
    }

    public function scopeWarning($query)
    {
        return $query->where('level', 'warning');
    }

    public function scopeInfo($query)
    {
        return $query->whereIn('level', ['debug', 'info', 'notice']);
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

    public static function logSystemEvent(array $data): self
    {
        return self::create(array_merge([
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'session_id' => session()?->getId(),
            'created_at' => now(),
        ], $data));
    }

    public static function debug(string $message, array $context = [], array $additionalData = []): self
    {
        return self::logSystemEvent(array_merge([
            'level' => 'debug',
            'message' => $message,
            'context' => $context,
        ], $additionalData));
    }

    public static function info(string $message, array $context = [], array $additionalData = []): self
    {
        return self::logSystemEvent(array_merge([
            'level' => 'info',
            'message' => $message,
            'context' => $context,
        ], $additionalData));
    }

    public static function notice(string $message, array $context = [], array $additionalData = []): self
    {
        return self::logSystemEvent(array_merge([
            'level' => 'notice',
            'message' => $message,
            'context' => $context,
        ], $additionalData));
    }

    public static function warning(string $message, array $context = [], array $additionalData = []): self
    {
        return self::logSystemEvent(array_merge([
            'level' => 'warning',
            'message' => $message,
            'context' => $context,
        ], $additionalData));
    }

    public static function error(string $message, \Throwable $exception = null, array $context = [], array $additionalData = []): self
    {
        $data = [
            'level' => 'error',
            'message' => $message,
            'context' => $context,
        ];

        if ($exception) {
            $data = array_merge($data, [
                'exception_class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stack_trace' => $exception->getTraceAsString(),
            ]);
        }

        return self::logSystemEvent(array_merge($data, $additionalData));
    }

    public static function critical(string $message, \Throwable $exception = null, array $context = [], array $additionalData = []): self
    {
        $data = [
            'level' => 'critical',
            'message' => $message,
            'context' => $context,
        ];

        if ($exception) {
            $data = array_merge($data, [
                'exception_class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stack_trace' => $exception->getTraceAsString(),
            ]);
        }

        return self::logSystemEvent(array_merge($data, $additionalData));
    }

    public static function alert(string $message, array $context = [], array $additionalData = []): self
    {
        return self::logSystemEvent(array_merge([
            'level' => 'alert',
            'message' => $message,
            'context' => $context,
        ], $additionalData));
    }

    public static function emergency(string $message, array $context = [], array $additionalData = []): self
    {
        return self::logSystemEvent(array_merge([
            'level' => 'emergency',
            'message' => $message,
            'context' => $context,
        ], $additionalData));
    }

    public static function logPerformance(string $action, float $executionTime, int $memoryUsage, array $context = []): self
    {
        return self::logSystemEvent([
            'level' => 'info',
            'channel' => 'performance',
            'message' => "Performance metric for {$action}",
            'action' => $action,
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'context' => $context,
        ]);
    }

    public static function logApiCall(string $endpoint, string $method, int $responseCode, float $executionTime, array $context = []): self
    {
        return self::logSystemEvent([
            'level' => $responseCode >= 400 ? 'warning' : 'info',
            'channel' => 'api',
            'message' => "API call: {$method} {$endpoint} - {$responseCode}",
            'action' => 'api_call',
            'context' => array_merge($context, [
                'endpoint' => $endpoint,
                'method' => $method,
                'response_code' => $responseCode,
            ]),
            'execution_time' => $executionTime,
        ]);
    }

    public static function logDatabaseQuery(string $query, float $executionTime, array $bindings = []): self
    {
        return self::logSystemEvent([
            'level' => $executionTime > 1.0 ? 'warning' : 'debug',
            'channel' => 'database',
            'message' => "Database query executed",
            'action' => 'database_query',
            'context' => [
                'query' => $query,
                'bindings' => $bindings,
            ],
            'execution_time' => $executionTime,
        ]);
    }

    public static function logJobExecution(string $jobClass, bool $success, float $executionTime, array $context = []): self
    {
        return self::logSystemEvent([
            'level' => $success ? 'info' : 'error',
            'channel' => 'queue',
            'message' => "Job execution: {$jobClass} - " . ($success ? 'Success' : 'Failed'),
            'action' => 'job_execution',
            'context' => array_merge($context, [
                'job_class' => $jobClass,
                'success' => $success,
            ]),
            'execution_time' => $executionTime,
        ]);
    }

    public static function getSystemStats(\DateTime $startDate, \DateTime $endDate): array
    {
        $logs = self::inDateRange($startDate, $endDate);

        return [
            'total_logs' => $logs->count(),
            'error_count' => $logs->error()->count(),
            'warning_count' => $logs->warning()->count(),
            'info_count' => $logs->info()->count(),
            'critical_count' => $logs->where('level', 'critical')->count(),
            'emergency_count' => $logs->where('level', 'emergency')->count(),
            'avg_execution_time' => $logs->avg('execution_time'),
            'avg_memory_usage' => $logs->avg('memory_usage'),
            'most_active_channels' => $logs->selectRaw('channel, COUNT(*) as count')
                                     ->groupBy('channel')
                                     ->orderByDesc('count')
                                     ->limit(5)
                                     ->pluck('count', 'channel')
                                     ->toArray(),
            'most_error_components' => $logs->error()
                                        ->selectRaw('component, COUNT(*) as count')
                                        ->groupBy('component')
                                        ->orderByDesc('count')
                                        ->limit(5)
                                        ->pluck('count', 'component')
                                        ->toArray(),
        ];
    }

    public static function getErrorTrends(\DateTime $startDate, \DateTime $endDate): array
    {
        return self::inDateRange($startDate, $endDate)
                   ->error()
                   ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->groupBy('date')
                   ->orderBy('date')
                   ->pluck('count', 'date')
                   ->toArray();
    }

    public static function getPerformanceMetrics(\DateTime $startDate, \DateTime $endDate): array
    {
        return self::inDateRange($startDate, $endDate)
                   ->where('channel', 'performance')
                   ->selectRaw('AVG(execution_time) as avg_time, MAX(execution_time) as max_time, AVG(memory_usage) as avg_memory')
                   ->first()
                   ->toArray();
    }
}
