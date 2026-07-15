<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class VibrationPatternService
{
    /**
     * Vibration patterns for different accessibility needs.
     */
    private static $patterns = [
        'emergency' => [
            'name' => 'Emergency Alert',
            'pattern' => [100, 200, 100, 200, 100, 200, 100, 200, 100, 200],
            'duration' => 2000, // 2 seconds
            'intensity' => 'high',
            'description' => 'Patrón de vibración para emergencias - 10 pulsos largos',
        ],
        'message_received' => [
            'name' => 'Message Received',
            'pattern' => [50, 50, 50, 50, 50, 50, 50, 50, 50, 50],
            'duration' => 500, // 0.5 seconds
            'intensity' => 'medium',
            'description' => 'Patrón corto para notificación de mensaje',
        ],
        'call_incoming' => [
            'name' => 'Incoming Call',
            'pattern' => [100, 100, 100, 100, 100, 100, 100, 100, 100],
            'duration' => 1500, // 1.5 seconds
            'intensity' => 'high',
            'description' => 'Patrón de llamada entrante - 10 pulsos largos',
        ],
        'notification' => [
            'name' => 'General Notification',
            'pattern' => [30, 30, 30, 30, 30, 30, 30, 30, 30],
            'duration' => 300, // 0.3 seconds
            'intensity' => 'low',
            'description' => 'Patrón suave para notificaciones generales',
        ],
        'error' => [
            'name' => 'Error',
            'pattern' => [200, 200, 200, 200, 200, 200, 200, 200, 200],
            'duration' => 1000, // 1 second
            'intensity' => 'high',
            'description' => 'Patrón de error - 5 pulsos muy fuertes',
        ],
        'success' => [
            'name' => 'Success',
            'pattern' => [80, 80, 80, 80, 80, 80, 80, 80, 80],
            'duration' => 800, // 0.8 seconds
            'intensity' => 'medium',
            'description' => 'Patrón de éxito - 10 pulsos medios',
        ],
        'warning' => [
            'name' => 'Warning',
            'pattern' => [60, 60, 60, 60, 60, 60, 60, 60, 60],
            'duration' => 600, // 0.6 seconds
            'intensity' => 'medium',
            'description' => 'Patrón de advertencia - 10 pulsos medios',
        ],
        'accessibility_mode' => [
            'name' => 'Accessibility Mode',
            'pattern' => [40, 40, 40, 40, 40, 40, 40, 40, 40],
            'duration' => 400, // 0.4 seconds
            'intensity' => 'low',
            'description' => 'Patrón para cambio de modo de accesibilidad',
        ],
        'battery_low' => [
            'name' => 'Battery Low',
            'pattern' => [150, 150, 150, 150, 150, 150, 150, 150, 150],
            'duration' => 3000, // 3 seconds
            'intensity' => 'high',
            'description' => 'Patrón de batería baja - 20 pulsos fuertes',
        ],
        'connection_lost' => [
            'name' => 'Connection Lost',
            'pattern' => [100, 100, 100, 100, 100, 100, 100, 100, 100],
            'duration' => 2000, // 2 seconds
            'intensity' => 'high',
            'description' => 'Patrón de conexión perdida - 10 pulsos largos',
        ],
        'sos' => [
            'name' => 'SOS Signal',
            'pattern' => [250, 250, 250, 250, 250, 250, 250, 250, 250],
            'duration' => 3000, // 3 seconds
            'intensity' => 'maximum',
            'description' => 'Patrón de SOS - 12 pulsos muy fuertes',
        ],
        'medical_reminder' => [
            'name' => 'Medical Reminder',
            'pattern' => [70, 70, 70, 70, 70, 70, 70, 70, 70],
            'duration' => 700, // 0.7 seconds
            'intensity' => 'medium',
            'description' => 'Patrón de recordatorio médico - 10 pulsos medios',
        ],
        'appointment_reminder' => [
            'name' => 'Appointment Reminder',
            'pattern' => [90, 90, 90, 90, 90, 90, 90, 90, 90],
            'duration' => 900, // 0.9 seconds
            'intensity' => 'medium',
            'description' => 'Patrón de recordatorio de cita - 10 pulsos medios',
        ],
    ];

    /**
     * Get vibration pattern by type.
     */
    public static function getPattern(string $type): array
    {
        if (isset(self::$patterns[$type])) {
            return self::$patterns[$type];
        }

        Log::warning('Vibration pattern not found', [
            'type' => $type,
            'available_types' => array_keys(self::$patterns),
        ]);

        return self::$patterns['notification']; // Default to notification
    }

    /**
     * Get all available patterns.
     */
    public static function getAllPatterns(): array
    {
        return self::$patterns;
    }

    /**
     * Create custom vibration pattern.
     */
    public static function createCustomPattern(string $name, array $pattern, int $duration, string $intensity): array
    {
        $customPattern = [
            'name' => $name,
            'pattern' => $pattern,
            'duration' => $duration,
            'intensity' => $intensity,
            'description' => "Patrón personalizado: {$name}",
            'is_custom' => true,
            'created_at' => now(),
        ];

        Log::info('Custom vibration pattern created', [
            'name' => $name,
            'duration' => $duration,
            'intensity' => $intensity,
            'pattern_count' => count($pattern),
        ]);

        return $customPattern;
    }

    /**
     * Convert vibration pattern to device format.
     */
    public static function convertToDeviceFormat(array $pattern): array
    {
        $devicePattern = [
            'type' => 'vibration',
            'pattern' => [],
            'duration' => $pattern['duration'],
            'intensity' => $pattern['intensity'],
            'repeat' => 1,
        ];

        // Convert pattern array to device-specific format
        foreach ($pattern['pattern'] as $index => $value) {
            $devicePattern['pattern'][] = [
                'timing' => $value,
                'amplitude' => self::mapIntensityToAmplitude($pattern['intensity']),
            ];
        }

        Log::info('Vibration pattern converted to device format', [
            'pattern_name' => $pattern['name'],
            'intensity' => $pattern['intensity'],
            'pattern_length' => count($pattern['pattern']),
        ]);

        return $devicePattern;
    }

    /**
     * Map intensity to device amplitude.
     */
    private static function mapIntensityToAmplitude(string $intensity): int
    {
        $amplitudeMap = [
            'low' => 50,
            'medium' => 75,
            'high' => 100,
            'maximum' => 150,
        ];

        return $amplitudeMap[$intensity] ?? 75;
    }

    /**
     * Save user's preferred vibration settings.
     */
    public static function saveUserPreferences(int $userId, array $preferences): bool
    {
        try {
            $key = "user_vibration_preferences_{$userId}";
            
            $preferencesData = [
                'default_pattern' => $preferences['default_pattern'] ?? 'notification',
                'intensity' => $preferences['intensity'] ?? 'medium',
                'duration' => $preferences['duration'] ?? null,
                'custom_patterns' => $preferences['custom_patterns'] ?? [],
                'haptic_feedback' => $preferences['haptic_feedback'] ?? true,
                'accessibility_mode' => $preferences['accessibility_mode'] ?? 'standard',
                'updated_at' => now(),
            ];

            Cache::put($key, $preferencesData, 86400); // 24 hours

            Log::info('User vibration preferences saved', [
                'user_id' => $userId,
                'default_pattern' => $preferences['default_pattern'],
                'intensity' => $preferences['intensity'],
                'custom_patterns_count' => count($preferences['custom_patterns'] ?? []),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to save user vibration preferences', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get user's vibration preferences.
     */
    public static function getUserPreferences(int $userId): array
    {
        try {
            $key = "user_vibration_preferences_{$userId}";
            $preferences = Cache::get($key, []);

            if (empty($preferences)) {
                return [
                    'default_pattern' => 'notification',
                    'intensity' => 'medium',
                    'duration' => null,
                    'custom_patterns' => [],
                    'haptic_feedback' => true,
                    'accessibility_mode' => 'standard',
                ];
            }

            return $preferences;

        } catch (\Exception $e) {
            Log::error('Failed to get user vibration preferences', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Trigger vibration for user.
     */
    public static function triggerVibration(int $userId, string $patternType, array $customData = []): bool
    {
        try {
            // Get user preferences
            $preferences = self::getUserPreferences($userId);
            
            // Get pattern
            if (!empty($customData)) {
                $pattern = self::createCustomPattern(
                    $customData['name'] ?? 'Custom',
                    $customData['pattern'] ?? [50, 50, 50, 50, 50],
                    $customData['duration'] ?? 500,
                    $customData['intensity'] ?? 'medium'
                );
            } else {
                $pattern = self::getPattern($patternType);
            }

            // Convert to device format
            $devicePattern = self::convertToDeviceFormat($pattern);

            // Store in Redis for real-time processing
            $vibrationKey = "vibration_trigger_{$userId}";
            $vibrationData = [
                'user_id' => $userId,
                'pattern_type' => $patternType,
                'pattern' => $devicePattern,
                'triggered_at' => now(),
                'preferences' => $preferences,
            ];

            Redis::setex($vibrationKey, 60, json_encode($vibrationData)); // 1 minute TTL

            Log::info('Vibration triggered for user', [
                'user_id' => $userId,
                'pattern_type' => $patternType,
                'pattern_name' => $pattern['name'],
                'duration' => $pattern['duration'],
                'intensity' => $pattern['intensity'],
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to trigger vibration', [
                'user_id' => $userId,
                'pattern_type' => $patternType,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get vibration statistics.
     */
    public static function getVibrationStatistics(int $userId = null): array
    {
        try {
            $stats = [
                'total_triggers' => 0,
                'patterns_used' => [],
                'most_used_pattern' => null,
                'average_intensity' => 'medium',
                'user_preferences' => [],
            ];

            if ($userId) {
                $stats['user_preferences'] = self::getUserPreferences($userId);
            }

            // Get statistics from Redis or cache
            $statsKey = 'vibration_statistics';
            $cachedStats = Cache::get($statsKey, []);

            if (!empty($cachedStats)) {
                $stats = array_merge($stats, $cachedStats);
            }

            Log::info('Vibration statistics retrieved', [
                'user_id' => $userId,
                'total_triggers' => $stats['total_triggers'],
                'patterns_used_count' => count($stats['patterns_used']),
            ]);

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get vibration statistics', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Test vibration pattern.
     */
    public static function testVibrationPattern(array $pattern): array
    {
        try {
            $testResult = [
                'pattern' => $pattern,
                'test_duration' => 0,
                'success' => false,
                'device_compatibility' => 'unknown',
                'issues' => [],
            ];

            // Simulate pattern testing
            $patternLength = count($pattern['pattern']);
            $expectedDuration = $pattern['duration'];
            
            // Check pattern validity
            if ($patternLength < 3) {
                $testResult['issues'][] = 'Pattern too short (minimum 3 pulses)';
            }

            if ($patternLength > 50) {
                $testResult['issues'][] = 'Pattern too long (maximum 50 pulses)';
            }

            if ($expectedDuration < 100) {
                $testResult['issues'][] = 'Duration too short (minimum 100ms)';
            }

            if ($expectedDuration > 5000) {
                $testResult['issues'][] = 'Duration too long (maximum 5000ms)';
            }

            // Check intensity validity
            $validIntensities = ['low', 'medium', 'high', 'maximum'];
            if (!in_array($pattern['intensity'], $validIntensities)) {
                $testResult['issues'][] = 'Invalid intensity level';
            }

            if (empty($testResult['issues'])) {
                $testResult['success'] = true;
                $testResult['test_duration'] = $expectedDuration;
                $testResult['device_compatibility'] = 'compatible';
            }

            Log::info('Vibration pattern test completed', [
                'pattern_name' => $pattern['name'],
                'success' => $testResult['success'],
                'issues_count' => count($testResult['issues']),
            ]);

            return $testResult;

        } catch (\Exception $e) {
            Log::error('Failed to test vibration pattern', [
                'pattern_name' => $pattern['name'] ?? 'Unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'issues' => ['Test failed due to system error'],
            ];
        }
    }

    /**
     * Optimize vibration pattern for device.
     */
    public static function optimizePatternForDevice(array $pattern, string $deviceType): array
    {
        try {
            $optimizedPattern = $pattern;
            
            switch ($deviceType) {
                case 'mobile':
                    // Optimize for mobile battery usage
                    $optimizedPattern['pattern'] = array_slice($pattern['pattern'], 0, 10);
                    $optimizedPattern['duration'] = min($pattern['duration'], 2000);
                    $optimizedPattern['intensity'] = $pattern['intensity'] === 'maximum' ? 'high' : $pattern['intensity'];
                    break;

                case 'wearable':
                    // Optimize for wearable devices
                    $optimizedPattern['pattern'] = array_map(function($value) {
                        return min($value, 150); // Limit amplitude for wearables
                    }, $pattern['pattern']);
                    $optimizedPattern['duration'] = min($pattern['duration'], 1500);
                    break;

                case 'desktop':
                    // No optimization needed for desktop
                    $optimizedPattern = $pattern;
                    break;

                default:
                    $optimizedPattern = $pattern;
                    break;
            }

            Log::info('Vibration pattern optimized for device', [
                'pattern_name' => $pattern['name'],
                'device_type' => $deviceType,
                'original_duration' => $pattern['duration'],
                'optimized_duration' => $optimizedPattern['duration'],
            ]);

            return $optimizedPattern;

        } catch (\Exception $e) {
            Log::error('Failed to optimize vibration pattern', [
                'pattern_name' => $pattern['name'] ?? 'Unknown',
                'device_type' => $deviceType,
                'error' => $e->getMessage(),
            ]);

            return $pattern;
        }
    }

    /**
     * Get accessibility mode patterns.
     */
    public static function getAccessibilityModePatterns(): array
    {
        return [
            'blind' => [
                'preferred_patterns' => ['emergency', 'message_received', 'call_incoming'],
                'intensity_preference' => 'high',
                'haptic_feedback' => true,
                'audio_cues' => true,
            ],
            'deaf' => [
                'preferred_patterns' => ['notification', 'warning', 'error'],
                'intensity_preference' => 'medium',
                'haptic_feedback' => true,
                'visual_cues' => true,
            ],
            'mute' => [
                'preferred_patterns' => ['message_received', 'notification', 'error'],
                'intensity_preference' => 'high',
                'haptic_feedback' => true,
                'visual_cues' => true,
            ],
            'mobility_impaired' => [
                'preferred_patterns' => ['emergency', 'notification', 'warning'],
                'intensity_preference' => 'medium',
                'haptic_feedback' => true,
                'extended_duration' => true,
            ],
            'cognitive_disability' => [
                'preferred_patterns' => ['notification', 'appointment_reminder', 'medical_reminder'],
                'intensity_preference' => 'low',
                'haptic_feedback' => true,
                'simple_patterns' => true,
            ],
        ];
    }

    /**
     * Clean up old vibration data.
     */
    public static function cleanupOldData(int $daysOld = 30): int
    {
        try {
            $cutoffDate = now()->subDays($daysOld);
            $cleanedCount = 0;

            // Clean up Redis keys
            $redis = Redis::connection();
            $keys = $redis->keys('vibration_trigger_*');
            
            foreach ($keys as $key) {
                $data = json_decode($redis->get($key), true);
                if ($data && isset($data['triggered_at'])) {
                    $triggerDate = \Carbon\Carbon::parse($data['triggered_at']);
                    if ($triggerDate->lt($cutoffDate)) {
                        $redis->del($key);
                        $cleanedCount++;
                    }
                }
            }

            // Clean up cache
            Cache::flush();

            Log::info('Vibration data cleanup completed', [
                'days_old' => $daysOld,
                'cleaned_count' => $cleanedCount,
            ]);

            return $cleanedCount;

        } catch (\Exception $e) {
            Log::error('Failed to cleanup vibration data', [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }
}
