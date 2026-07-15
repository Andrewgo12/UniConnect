<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class IpBlockingService
{
    /**
     * Block an IP address.
     */
    public static function blockIp(string $ip, int $duration = 3600, string $reason = 'Security violation'): bool
    {
        try {
            $blockedIps = self::getBlockedIps();
            $blockedIps[$ip] = [
                'blocked_at' => now(),
                'duration' => $duration,
                'reason' => $reason,
                'blocked_by' => 'security_middleware',
            ];

            Cache::put('blocked_ips', $blockedIps, $duration);
            
            // Add to Redis for real-time blocking
            Redis::setex("blocked_ip:{$ip}", $duration, json_encode([
                'blocked' => true,
                'reason' => $reason,
                'timestamp' => now()->timestamp,
            ]));

            Log::info('IP blocked successfully', [
                'ip' => $ip,
                'duration' => $duration,
                'reason' => $reason,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to block IP', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Unblock an IP address.
     */
    public static function unblockIp(string $ip): bool
    {
        try {
            $blockedIps = self::getBlockedIps();
            unset($blockedIps[$ip]);
            
            Cache::put('blocked_ips', $blockedIps, 3600);
            
            // Remove from Redis
            Redis::del("blocked_ip:{$ip}");

            Log::info('IP unblocked successfully', [
                'ip' => $ip,
                'timestamp' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to unblock IP', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if IP is blocked.
     */
    public static function isIpBlocked(string $ip): bool
    {
        $blockedIps = self::getBlockedIps();
        
        // Check cache first
        if (isset($blockedIps[$ip])) {
            $blockedAt = $blockedIps[$ip]['blocked_at'];
            $duration = $blockedIps[$ip]['duration'] ?? 3600;
            
            // Check if block has expired
            if (now()->diffInMinutes($blockedAt) < $duration) {
                return true;
            }
        }

        // Check Redis for real-time blocking
        $redisBlocked = Redis::get("blocked_ip:{$ip}");
        if ($redisBlocked) {
            $blockedData = json_decode($redisBlocked, true);
            return $blockedData->blocked ?? false;
        }

        return false;
    }

    /**
     * Get list of blocked IPs.
     */
    public static function getBlockedIps(): array
    {
        return Cache::get('blocked_ips', []);
    }

    /**
     * Get blocked IPs with details.
     */
    public static function getBlockedIpsWithDetails(): array
    {
        $blockedIps = self::getBlockedIps();
        $detailedIps = [];

        foreach ($blockedIps as $ip => $details) {
            $detailedIps[$ip] = array_merge($details, [
                'is_blocked' => true,
                'remaining_time' => $details['duration'] - now()->diffInMinutes($details['blocked_at']),
            ]);
        }

        return $detailedIps;
    }

    /**
     * Clean up expired blocks.
     */
    public static function cleanupExpiredBlocks(): int
    {
        $blockedIps = self::getBlockedIps();
        $cleanedCount = 0;
        $updatedIps = [];

        foreach ($blockedIps as $ip => $details) {
            $blockedAt = $details['blocked_at'];
            $duration = $details['duration'] ?? 3600;
            
            if (now()->diffInMinutes($blockedAt) < $duration) {
                $updatedIps[$ip] = $details;
            } else {
                $cleanedCount++;
                Log::info('Expired IP block cleaned up', [
                    'ip' => $ip,
                    'blocked_at' => $blockedAt,
                    'duration' => $duration,
                ]);
            }
        }

        if ($cleanedCount > 0) {
            Cache::put('blocked_ips', $updatedIps, 3600);
            Log::info('IP block cleanup completed', [
                'cleaned_count' => $cleanedCount,
                'remaining_blocks' => count($updatedIps),
            ]);
        }

        return $cleanedCount;
    }

    /**
     * Get IP geolocation.
     */
    public static function getIpGeolocation(string $ip): array
    {
        try {
            $response = file_get_contents("http://ip-api.com/json/{$ip}");
            $data = json_decode($response, true);

            if ($data && isset($data['country'])) {
                return [
                    'ip' => $ip,
                    'country' => $data['country'] ?? 'Unknown',
                    'region' => $data['region'] ?? 'Unknown',
                    'city' => $data['city'] ?? 'Unknown',
                    'isp' => $data['isp'] ?? 'Unknown',
                    'is_proxy' => $data['proxy'] ?? false,
                ];
            }

        } catch (\Exception $e) {
            Log::error('Failed to get IP geolocation', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'ip' => $ip,
            'country' => 'Unknown',
            'region' => 'Unknown',
            'city' => 'Unknown',
            'isp' => 'Unknown',
            'is_proxy' => false,
        ];
    }

    /**
     * Check if IP is from private range.
     */
    public static function isPrivateIp(string $ip): bool
    {
        $privateRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            '127.0.0.0/8',
        ];

        foreach ($privateRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in range.
     */
    private static function ipInRange(string $ip, string $range): bool
    {
        [$rangeIp, $mask] = explode('/', $range);
        $rangeDecimal = ip2long($rangeIp);
        $maskDecimal = ip2long($mask);
        $ipDecimal = ip2long($ip);

        return ($ipDecimal & $maskDecimal) === ($rangeDecimal & $maskDecimal);
    }

    /**
     * Get IP reputation score.
     */
    public static function getIpReputation(string $ip): array
    {
        try {
            // Simulate reputation check (in production, use real service)
            $reputation = [
                'score' => 75, // 0-100 scale
                'categories' => [
                    'spam' => false,
                    'malicious' => false,
                    'suspicious' => false,
                    'proxy' => false,
                    'vpn' => false,
                ],
                'last_seen' => now()->subDays(7)->toDateString(),
                'sources' => ['project_honeypot', 'spam_blacklist'],
            ];

            Log::info('IP reputation checked', [
                'ip' => $ip,
                'reputation_score' => $reputation['score'],
            ]);

            return $reputation;

        } catch (\Exception $e) {
            Log::error('Failed to get IP reputation', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'score' => 50,
            'categories' => [
                'spam' => false,
                'malicious' => false,
                'suspicious' => false,
                'proxy' => false,
                'vpn' => false,
            ],
            'last_seen' => null,
            'sources' => [],
        ];
    }

    /**
     * Analyze IP patterns.
     */
    public static function analyzeIpPatterns(string $ip): array
    {
        $patterns = [
            'is_tor' => $this->isTorExitNode($ip),
            'is_datacenter' => $this->isDatacenterIp($ip),
            'is_mobile' => $this->isMobileIp($ip),
            'is_cloud' => $this->isCloudProvider($ip),
        ];

        Log::info('IP pattern analysis completed', [
            'ip' => $ip,
            'patterns' => $patterns,
        ]);

        return $patterns;
    }

    /**
     * Check if IP is Tor exit node.
     */
    private static function isTorExitNode(string $ip): bool
    {
        $torExitNodes = Cache::get('tor_exit_nodes', []);
        
        return in_array($ip, $torExitNodes);
    }

    /**
     * Check if IP is from datacenter.
     */
    private static function isDatacenterIp(string $ip): bool
    {
        $datacenterRanges = [
            '8.8.0.0/16',     // Google Cloud
            '52.0.0.0/16',     // AWS
            '13.0.0.0/16',     // AWS
            '104.16.0.0/13',    // AWS
            '20.0.0.0/16',     // AWS
            '54.0.0.0/16',     // AWS
            '172.217.0.0/16',   // AWS
            '204.236.0.0/16',   // AWS
            '107.20.0.0/16',    // AWS
            '108.59.0.0/16',    // AWS
            '147.28.0.0/16',    // AWS
            '23.0.0.0/16',     // AWS
            '64.233.0.0/16',    // AWS
            '70.0.0.0/16',     // AWS
            '72.44.0.0/16',     // AWS
            '54.232.0.0/16',    // AWS
            '205.251.0.0/16',   // AWS
            '216.239.0.0/16',   // AWS
        ];

        foreach ($datacenterRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is from mobile provider.
     */
    private static function isMobileIp(string $ip): bool
    {
        $mobileRanges = [
            '100.0.0.0/8',     // Verizon
            '108.0.0.0/8',     // AT&T
            '174.0.0.0/8',     // AT&T
            '166.0.0.0/8',     // AT&T
            '72.0.0.0/8',     // AT&T
            '199.0.0.0/8',     // AT&T
            '71.0.0.0/8',     // AT&T
            '75.0.0.0/8',     // AT&T
            '97.0.0.0/8',     // AT&T
            '74.0.0.0/8',     // AT&T
            '69.0.0.0/8',     // AT&T
            '67.0.0.0/8',     // AT&T
            '65.0.0.0/8',     // AT&T
            '66.0.0.0/8',     // AT&T
            '70.0.0.0/8',     // AT&T
            '64.0.0.0/8',     // AT&T
        ];

        foreach ($mobileRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is from cloud provider.
     */
    private static function isCloudProvider(string $ip): bool
    {
        $cloudProviders = [
            'cloudflare' => ['103.21.244.0/22', '104.16.0.0/12'],
            'aws' => ['52.0.0.0/16', '13.0.0.0/16'],
            'azure' => ['20.0.0.0/16', '40.64.0.0/10'],
            'google' => ['8.8.0.0/16', '8.34.0.0/15'],
            'digitalocean' => ['192.0.2.0/24'],
            'linode' => ['172.104.0.0/16', '172.105.0.0/16'],
        ];

        foreach ($cloudProviders as $provider => $ranges) {
            foreach ($ranges as $range) {
                if ($this->ipInRange($ip, $range)) {
                    return true;
                }
            }
        }

        return false;
    }
}
