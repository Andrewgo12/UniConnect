<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type', // login, logout, password_change, failed_login, account_locked, etc.
        'severity', // low, medium, high, critical
        'description',
        'ip_address',
        'user_agent',
        'device_type',
        'location',
        'success',
        'failure_reason',
        'resource_type', // user, message, conversation, medical_record, etc.
        'resource_id',
        'action', // create, read, update, delete, access_denied, etc.
        'previous_value',
        'new_value',
        'session_id',
        'request_id',
        'metadata',
    ];

    protected $casts = [
        'success' => 'boolean',
        'previous_value' => 'json',
        'new_value' => 'json',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeHigh($query)
    {
        return $query->whereIn('severity', ['high', 'critical']);
    }

    public function scopeByResource($query, string $resourceType, int $resourceId = null)
    {
        $query->where('resource_type', $resourceType);
        if ($resourceId) {
            $query->where('resource_id', $resourceId);
        }
        return $query;
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
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

    public static function logSecurityEvent(array $data): self
    {
        return self::create(array_merge([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'created_at' => now(),
        ], $data));
    }

    public static function logLoginAttempt(User $user, bool $success, string $failureReason = null): self
    {
        return self::logSecurityEvent([
            'user_id' => $user->id,
            'event_type' => 'login',
            'severity' => $success ? 'low' : 'medium',
            'description' => $success ? 'Usuario inició sesión exitosamente' : 'Intento de inicio de sesión fallido',
            'success' => $success,
            'failure_reason' => $failureReason,
            'device_type' => self::detectDeviceType(),
            'location' => self::getLocation(),
        ]);
    }

    public static function logLogout(User $user): self
    {
        return self::logSecurityEvent([
            'user_id' => $user->id,
            'event_type' => 'logout',
            'severity' => 'low',
            'description' => 'Usuario cerró sesión',
            'success' => true,
            'device_type' => self::detectDeviceType(),
        ]);
    }

    public static function logPasswordChange(User $user, bool $success): self
    {
        return self::logSecurityEvent([
            'user_id' => $user->id,
            'event_type' => 'password_change',
            'severity' => 'medium',
            'description' => $success ? 'Contraseña cambiada exitosamente' : 'Error al cambiar contraseña',
            'success' => $success,
            'device_type' => self::detectDeviceType(),
        ]);
    }

    public static function logAccountLocked(User $user, string $reason): self
    {
        return self::logSecurityEvent([
            'user_id' => $user->id,
            'event_type' => 'account_locked',
            'severity' => 'high',
            'description' => 'Cuenta de usuario bloqueada',
            'failure_reason' => $reason,
            'success' => false,
        ]);
    }

    public static function logUnauthorizedAccess(User $user = null, string $resourceType, int $resourceId = null): self
    {
        return self::logSecurityEvent([
            'user_id' => $user?->id,
            'event_type' => 'unauthorized_access',
            'severity' => 'high',
            'description' => 'Intento de acceso no autorizado',
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => 'access_denied',
            'success' => false,
            'ip_address' => request()->ip(),
        ]);
    }

    public static function logDataAccess(User $user, string $resourceType, int $resourceId, string $action): self
    {
        return self::logSecurityEvent([
            'user_id' => $user->id,
            'event_type' => 'data_access',
            'severity' => 'low',
            'description' => "Acceso a datos: {$action} {$resourceType}",
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'success' => true,
        ]);
    }

    public static function logDataModification(User $user, string $resourceType, int $resourceId, string $action, $previousValue = null, $newValue = null): self
    {
        return self::logSecurityEvent([
            'user_id' => $user->id,
            'event_type' => 'data_modification',
            'severity' => 'medium',
            'description' => "Modificación de datos: {$action} {$resourceType}",
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'previous_value' => $previousValue,
            'new_value' => $newValue,
            'success' => true,
        ]);
    }

    public static function logSuspiciousActivity(string $description, array $additionalData = []): self
    {
        return self::logSecurityEvent(array_merge([
            'event_type' => 'suspicious_activity',
            'severity' => 'high',
            'description' => $description,
            'success' => false,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $additionalData));
    }

    public static function logSecurityBreach(string $description, array $additionalData = []): self
    {
        return self::logSecurityEvent(array_merge([
            'event_type' => 'security_breach',
            'severity' => 'critical',
            'description' => $description,
            'success' => false,
            'ip_address' => request()->ip(),
        ], $additionalData));
    }

    public static function getSecurityStats(\DateTime $startDate, \DateTime $endDate): array
    {
        $logs = self::inDateRange($startDate, $endDate);

        return [
            'total_events' => $logs->count(),
            'failed_attempts' => $logs->where('success', false)->count(),
            'success_rate' => $logs->where('success', true)->count() / max(1, $logs->count()) * 100,
            'critical_events' => $logs->where('severity', 'critical')->count(),
            'high_severity_events' => $logs->where('severity', 'high')->count(),
            'unique_ips' => $logs->distinct('ip_address')->count('ip_address'),
            'most_common_events' => $logs->selectRaw('event_type, COUNT(*) as count')
                                   ->groupBy('event_type')
                                   ->orderByDesc('count')
                                   ->limit(5)
                                   ->pluck('count', 'event_type')
                                   ->toArray(),
        ];
    }

    public static function getFailedLoginAttempts(\DateTime $startDate, \DateTime $endDate): array
    {
        return self::inDateRange($startDate, $endDate)
                   ->where('event_type', 'login')
                   ->where('success', false)
                   ->selectRaw('ip_address, COUNT(*) as attempts')
                   ->groupBy('ip_address')
                   ->orderByDesc('attempts')
                   ->limit(10)
                   ->get()
                   ->toArray();
    }

    public static function getSuspiciousIPs(\DateTime $startDate, \DateTime $endDate, int $threshold = 10): array
    {
        return self::inDateRange($startDate, $endDate)
                   ->where('success', false)
                   ->selectRaw('ip_address, COUNT(*) as failed_attempts')
                   ->groupBy('ip_address')
                   ->having('failed_attempts', '>=', $threshold)
                   ->orderByDesc('failed_attempts')
                   ->pluck('failed_attempts', 'ip_address')
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

    private static function getLocation(): ?string
    {
        // This would typically use a geolocation service
        // For now, return null or implement basic IP-based location
        return null;
    }
}
