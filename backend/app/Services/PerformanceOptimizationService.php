<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class PerformanceOptimizationService
{
    /**
     * Optimize database queries.
     */
    public static function optimizeDatabaseQueries(): array
    {
        $start = microtime(true);
        
        try {
            // Enable query log
            DB::enableQueryLog();
            
            // Simulate some queries
            $queries = DB::getQueryLog();
            
            $slowQueries = [];
            $totalTime = 0;
            
            foreach ($queries as $query) {
                $totalTime += $query['time'];
                if ($query['time'] > 1000) { // More than 1 second
                    $slowQueries[] = $query;
                }
            }
            
            // Disable query log
            DB::disableQueryLog();
            
            $optimization = [
                'total_queries' => count($queries),
                'slow_queries' => count($slowQueries),
                'total_time' => $totalTime,
                'average_time' => $totalTime / max(1, count($queries)),
                'optimizations' => [
                    'add_indexes' => self::suggestIndexes($queries),
                    'optimize_joins' => self::optimizeJoins($queries),
                    'cache_results' => self::suggestCaching($queries),
                ],
            ];
            
            Log::info('Database optimization analysis completed', $optimization);
            
            return $optimization;
            
        } catch (\Exception $e) {
            Log::error('Database optimization failed', [
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Optimize cache performance.
     */
    public static function optimizeCachePerformance(): array
    {
        try {
            $cacheStats = [
                'redis_memory' => self::getRedisMemoryUsage(),
                'cache_hit_rate' => self::getCacheHitRate(),
                'cache_size' => self::getCacheSize(),
                'expired_keys' => self::getExpiredKeysCount(),
                'memory_fragmentation' => self::getMemoryFragmentation(),
            ];
            
            $optimizations = [
                'increase_memory_limit' => $cacheStats['redis_memory'] > 512 ? true : false,
                'optimize_key_patterns' => self::optimizeKeyPatterns(),
                'implement_cache_warming' => $cacheStats['cache_hit_rate'] < 80 ? true : false,
                'cleanup_expired_keys' => $cacheStats['expired_keys'] > 1000 ? true : false,
            ];
            
            Log::info('Cache performance analysis completed', [
                'stats' => $cacheStats,
                'optimizations' => $optimizations,
            ]);
            
            return array_merge($cacheStats, $optimizations);
            
        } catch (\Exception $e) {
            Log::error('Cache optimization failed', [
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Optimize API response times.
     */
    public static function optimizeApiPerformance(): array
    {
        try {
            $apiStats = [
                'average_response_time' => self::getAverageResponseTime(),
                'slow_endpoints' => self::getSlowEndpoints(),
                'error_rate' => self::getErrorRate(),
                'concurrent_users' => self::getConcurrentUsers(),
                'bandwidth_usage' => self::getBandwidthUsage(),
            ];
            
            $optimizations = [
                'implement_response_caching' => $apiStats['average_response_time'] > 500 ? true : false,
                'add_rate_limiting' => $apiStats['concurrent_users'] > 100 ? true : false,
                'optimize_database_queries' => count($apiStats['slow_endpoints']) > 5 ? true : false,
                'implement_compression' => $apiStats['bandwidth_usage'] > 1000000 ? true : false, // 1MB/s
            ];
            
            Log::info('API performance analysis completed', [
                'stats' => $apiStats,
                'optimizations' => $optimizations,
            ]);
            
            return array_merge($apiStats, $optimizations);
            
        } catch (\Exception $e) {
            Log::error('API optimization failed', [
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Get Redis memory usage.
     */
    private static function getRedisMemoryUsage(): float
    {
        try {
            $info = Redis::info('memory');
            return $info['used_memory'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get cache hit rate.
     */
    private static function getCacheHitRate(): float
    {
        try {
            $stats = Redis::info('stats');
            $hits = $stats['keyspace_hits'] ?? 0;
            $misses = $stats['keyspace_misses'] ?? 1;
            
            return $hits > 0 ? ($hits / ($hits + $misses)) * 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get cache size.
     */
    private static function getCacheSize(): int
    {
        try {
            $info = Redis::info('memory');
            return $info['used_memory'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get expired keys count.
     */
    private static function getExpiredKeysCount(): int
    {
        try {
            $keys = Redis::keys('*');
            $expiredCount = 0;
            
            foreach ($keys as $key) {
                $ttl = Redis::ttl($key);
                if ($ttl > 0 && $ttl < 3600) { // Less than 1 hour
                    $expiredCount++;
                }
            }
            
            return $expiredCount;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get memory fragmentation.
     */
    private static function getMemoryFragmentation(): float
    {
        try {
            $info = Redis::info('memory');
            $used = $info['used_memory'] ?? 0;
            $rss = $info['used_memory_rss'] ?? $used;
            
            return $rss > 0 ? ($used / $rss) * 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get average response time.
     */
    private static function getAverageResponseTime(): float
    {
        try {
            $responseTimes = Cache::get('recent_response_times', []);
            if (empty($responseTimes)) {
                return 0;
            }
            
            return array_sum($responseTimes) / count($responseTimes);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get slow endpoints.
     */
    private static function getSlowEndpoints(): array
    {
        try {
            $slowEndpoints = Cache::get('slow_endpoints', []);
            return array_filter($slowEndpoints, function ($endpoint) {
                return $endpoint['response_time'] > 1000;
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get error rate.
     */
    private static function getErrorRate(): float
    {
        try {
            $totalRequests = Cache::get('total_requests', 0);
            $errorRequests = Cache::get('error_requests', 1);
            
            return $totalRequests > 0 ? ($errorRequests / $totalRequests) * 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get concurrent users.
     */
    private static function getConcurrentUsers(): int
    {
        try {
            return Redis::scard('active_sessions') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get bandwidth usage.
     */
    private static function getBandwidthUsage(): float
    {
        try {
            return Redis::get('bandwidth_usage') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Suggest database indexes.
     */
    private static function suggestIndexes(array $queries): array
    {
        $suggestions = [];
        $whereClauses = [];
        
        foreach ($queries as $query) {
            if (preg_match('/WHERE\s+(\w+)/i', $query['query'], $matches)) {
                $whereClauses[] = $matches[1];
            }
        }
        
        $commonIndexes = [
            'users.email' => 'users_email_index',
            'messages.created_at' => 'messages_created_at_index',
            'emergencies.status' => 'emergencies_status_index',
            'analytics.user_id' => 'analytics_user_id_index',
        ];
        
        foreach ($whereClauses as $column) {
            if (isset($commonIndexes[$column])) {
                $suggestions[] = "Add index: {$commonIndexes[$column]}";
            }
        }
        
        return array_unique($suggestions);
    }

    /**
     * Optimize joins.
     */
    private static function optimizeJoins(array $queries): array
    {
        $suggestions = [];
        
        foreach ($queries as $query) {
            if (preg_match('/JOIN\s+(\w+)/i', $query['query'], $matches)) {
                $suggestions[] = "Optimize JOIN on {$matches[1]} - add index";
            }
        }
        
        return array_unique($suggestions);
    }

    /**
     * Suggest caching.
     */
    private static function suggestCaching(array $queries): array
    {
        $suggestions = [];
        
        foreach ($queries as $query) {
            if (preg_match('/SELECT\s+.*FROM\s+(\w+)/i', $query['query'], $matches)) {
                $table = $matches[1];
                if (in_array($table, ['users', 'settings', 'analytics'])) {
                    $suggestions[] = "Cache {$table} queries - results change infrequently";
                }
            }
        }
        
        return array_unique($suggestions);
    }

    /**
     * Optimize key patterns.
     */
    private static function optimizeKeyPatterns(): array
    {
        try {
            $keys = Redis::keys('*');
            $patterns = [];
            
            foreach ($keys as $key) {
                if (strlen($key) > 50) {
                    $patterns[] = "Long key detected: {$key} - consider shortening";
                }
                
                if (strpos($key, ':') !== false) {
                    $patterns[] = "Complex key pattern: {$key} - consider flattening";
                }
            }
            
            return array_unique($patterns);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Clear performance metrics.
     */
    public static function clearPerformanceMetrics(): void
    {
        try {
            Cache::forget('recent_response_times');
            Cache::forget('slow_endpoints');
            Cache::forget('total_requests');
            Cache::forget('error_requests');
            Redis::del('bandwidth_usage');
            
            Log::info('Performance metrics cleared');
        } catch (\Exception $e) {
            Log::error('Failed to clear performance metrics', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate performance report.
     */
    public static function generatePerformanceReport(): array
    {
        $dbOptimization = self::optimizeDatabaseQueries();
        $cacheOptimization = self::optimizeCachePerformance();
        $apiOptimization = self::optimizeApiPerformance();
        
        $report = [
            'generated_at' => now(),
            'database' => $dbOptimization,
            'cache' => $cacheOptimization,
            'api' => $apiOptimization,
            'summary' => [
                'total_optimizations' => count($dbOptimization['optimizations']) + count($cacheOptimization['optimizations']) + count($apiOptimization['optimizations']),
                'critical_issues' => array_filter([
                    $dbOptimization['optimizations'],
                    $cacheOptimization['optimizations'],
                    $apiOptimization['optimizations'],
                ], function ($opt) {
                    return strpos($opt, 'increase_') !== false || strpos($opt, 'implement_') !== false;
                }),
            ],
        ];
        
        Log::info('Performance report generated', $report);
        
        return $report;
    }
}
