<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SecurityValidationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $uri = $request->path();

        // Log incoming request
        Log::info('Security validation middleware triggered', [
            'ip' => $ip,
            'user_agent' => $userAgent,
            'uri' => $uri,
            'timestamp' => now(),
        ]);

        // Check if IP is blocked
        if ($this->isIpBlocked($ip)) {
            Log::warning('Blocked IP attempt detected', [
                'ip' => $ip,
                'user_agent' => $userAgent,
                'uri' => $uri,
            ]);

            return response()->json([
                'error' => 'IP_BLOCKED',
                'message' => 'Su dirección IP ha sido bloqueada temporalmente',
                'code' => 403,
            ], 403);
        }

        // Check for suspicious user agents
        if ($this->isSuspiciousUserAgent($userAgent)) {
            Log::warning('Suspicious user agent detected', [
                'ip' => $ip,
                'user_agent' => $userAgent,
                'uri' => $uri,
            ]);

            return response()->json([
                'error' => 'SUSPICIOUS_UA',
                'message' => 'Se detectó un agente de usuario sospechoso',
                'code' => 403,
            ], 403);
        }

        // Check for rate limiting
        if ($this->isRateLimited($ip)) {
            Log::warning('Rate limit exceeded', [
                'ip' => $ip,
                'user_agent' => $userAgent,
                'uri' => $uri,
            ]);

            return response()->json([
                'error' => 'RATE_LIMITED',
                'message' => 'Ha excedido el límite de solicitudes. Por favor, intente más tarde.',
                'code' => 429,
            ], 429);
        }

        // Check for SQL injection patterns
        if ($this->containsSqlInjection($request)) {
            Log::warning('SQL injection attempt detected', [
                'ip' => $ip,
                'user_agent' => $userAgent,
                'uri' => $uri,
                'input' => $request->all(),
            ]);

            return response()->json([
                'error' => 'INVALID_INPUT',
                'message' => 'Se detectó un intento de inyección SQL',
                'code' => 400,
            ], 400);
        }

        // Check for XSS patterns
        if ($this->containsXss($request)) {
            Log::warning('XSS attempt detected', [
                'ip' => $ip,
                'user_agent' => $userAgent,
                'uri' => $uri,
                'input' => $request->all(),
            ]);

            return response()->json([
                'error' => 'INVALID_INPUT',
                'message' => 'Se detectó un intento de ataque XSS',
                'code' => 400,
            ], 400);
        }

        // Log successful validation
        Log::info('Security validation passed', [
            'ip' => $ip,
            'user_agent' => $userAgent,
            'uri' => $uri,
            'timestamp' => now(),
        ]);

        return $next($request);
    }

    /**
     * Check if IP is blocked.
     */
    private function isIpBlocked(string $ip): bool
    {
        $blockedIps = Cache::get('blocked_ips', []);
        
        // Check against known malicious IP ranges
        $maliciousRanges = [
            '0.0.0.0/8',      // IANA reserved
            '127.0.0.0/8',     // Loopback
            '169.254.0.0/16',   // Link-local
            '172.16.0.0/12',    // Private
            '192.168.0.0/16',   // Private
            '224.0.0.0/4',     // Multicast
        ];

        foreach ($maliciousRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return in_array($ip, $blockedIps);
    }

    /**
     * Check if user agent is suspicious.
     */
    private function isSuspiciousUserAgent(string $userAgent): bool
    {
        $suspiciousPatterns = [
            '/bot/i',
            '/crawler/i',
            '/scanner/i',
            '/sqlmap/i',
            '/nmap/i',
            '/metasploit/i',
            '/nikto/i',
            '/burp/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if request is rate limited.
     */
    private function isRateLimited(string $ip): bool
    {
        $key = "rate_limit_{$ip}";
        $requests = Cache::get($key, 0);
        
        // Allow 100 requests per minute
        $limit = 100;
        $window = 60; // seconds

        if ($requests >= $limit) {
            return true;
        }

        Cache::put($key, $requests + 1, $window);
        return false;
    }

    /**
     * Check for SQL injection patterns.
     */
    private function containsSqlInjection(Request $request): bool
    {
        $sqlPatterns = [
            '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|JOIN)\b/i',
            '/(\'|\"|;|--|\/\*|\*\/)/i',
            '/\bOR\b.*\bSELECT\b/i',
            '/\bAND\b.*\bSELECT\b/i',
        ];

        $input = json_encode($request->all());
        
        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for XSS patterns.
     */
    private function containsXss(Request $request): bool
    {
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>))*[^<]*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>))*[^<]*<\/iframe>/mi',
            '/<object\b[^<]*(?:(?!<\/object>))*[^<]*<\/object>/mi',
            '/<embed\b[^<]*(?:(?!<\/embed>))*[^<]*<\/embed>/mi',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<img[^>]*src[^>]*javascript:/i',
        ];

        $input = json_encode($request->all());
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in range.
     */
    private function ipInRange(string $ip, string $range): bool
    {
        [$rangeIp, $mask] = explode('/', $range);
        $rangeDecimal = ip2long($rangeIp);
        $maskDecimal = ip2long($mask);
        $ipDecimal = ip2long($ip);

        return ($ipDecimal & $maskDecimal) === ($rangeDecimal & $maskDecimal);
    }

    /**
     * Block an IP address.
     */
    public static function blockIp(string $ip, int $duration = 3600): void
    {
        $blockedIps = Cache::get('blocked_ips', []);
        $blockedIps[$ip] = now()->addSeconds($duration);
        Cache::put('blocked_ips', $blockedIps, $duration);
        
        Log::info('IP blocked', [
            'ip' => $ip,
            'duration' => $duration,
            'timestamp' => now(),
        ]);
    }

    /**
     * Unblock an IP address.
     */
    public static function unblockIp(string $ip): void
    {
        $blockedIps = Cache::get('blocked_ips', []);
        unset($blockedIps[$ip]);
        Cache::put('blocked_ips', $blockedIps, 3600);
        
        Log::info('IP unblocked', [
            'ip' => $ip,
            'timestamp' => now(),
        ]);
    }
}
