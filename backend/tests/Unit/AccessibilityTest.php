<?php

namespace Tests\Unit;

use App\Models\AccessibilityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccessibilityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $accessibilityLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'accessibility_needs' => ['blind', 'deaf', 'mute'],
        ]);
        
        $this->accessibilityLog = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'feature' => 'screen_reader',
            'action' => 'enabled',
            'device_type' => 'mobile',
            'platform' => 'ios',
            'success' => true,
            'response_time' => 150,
            'language' => 'es-CO',
            'accessibility_mode' => 'screen_reader',
            'location' => 'Bogotá',
            'timezone' => 'America/Bogota',
        ]);
    }

    /**
     * Test accessibility log creation.
     */
    public function test_accessibility_log_can_be_created(): void
    {
        $logData = [
            'user_id' => $this->user->id,
            'feature' => 'voice_commands',
            'action' => 'activated',
            'device_type' => 'desktop',
            'platform' => 'windows',
            'success' => true,
            'response_time' => 200,
            'language' => 'en-US',
            'accessibility_mode' => 'voice_control',
            'location' => 'New York',
            'timezone' => 'America/New_York',
        ];

        $log = AccessibilityLog::create($logData);

        $this->assertDatabaseHas('accessibility_logs', $logData);
        $this->assertEquals('voice_commands', $log->feature);
        $this->assertEquals('activated', $log->action);
        $this->assertEquals('desktop', $log->device_type);
        $this->assertTrue($log->success);
    }

    /**
     * Test accessibility log user relationship.
     */
    public function test_accessibility_log_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->accessibilityLog->user);
        $this->assertEquals($this->user->id, $this->accessibilityLog->user->id);
    }

    /**
     * Test accessibility log features.
     */
    public function test_accessibility_log_features(): void
    {
        $features = ['screen_reader', 'voice_commands', 'high_contrast', 'large_text', 'sign_language'];

        foreach ($features as $feature) {
            $log = AccessibilityLog::factory()->create([
                'user_id' => $this->user->id,
                'feature' => $feature,
                'action' => 'used',
                'success' => true,
            ]);

            $this->assertEquals($feature, $log->feature);
            $this->assertEquals('used', $log->action);
        }
    }

    /**
     * Test accessibility log device types.
     */
    public function test_accessibility_log_device_types(): void
    {
        $deviceTypes = ['desktop', 'mobile', 'tablet', 'voice_assistant'];

        foreach ($deviceTypes as $deviceType) {
            $log = AccessibilityLog::factory()->create([
                'user_id' => $this->user->id,
                'device_type' => $deviceType,
                'action' => 'login',
                'success' => true,
            ]);

            $this->assertEquals($deviceType, $log->device_type);
            $this->assertEquals('login', $log->action);
        }
    }

    /**
     * Test accessibility log platforms.
     */
    public function test_accessibility_log_platforms(): void
    {
        $platforms = ['windows', 'macos', 'linux', 'ios', 'android'];

        foreach ($platforms as $platform) {
            $log = AccessibilityLog::factory()->create([
                'user_id' => $this->user->id,
                'platform' => $platform,
                'action' => 'startup',
                'success' => true,
            ]);

            $this->assertEquals($platform, $log->platform);
            $this->assertEquals('startup', $log->action);
        }
    }

    /**
     * Test accessibility log actions.
     */
    public function test_accessibility_log_actions(): void
    {
        $actions = ['enabled', 'disabled', 'used', 'failed', 'error', 'login', 'logout'];

        foreach ($actions as $action) {
            $log = AccessibilityLog::factory()->create([
                'user_id' => $this->user->id,
                'action' => $action,
                'success' => $action !== 'failed' && $action !== 'error',
            ]);

            $this->assertEquals($action, $log->action);
            $this->assertEquals($action !== 'failed' && $action !== 'error', $log->success);
        }
    }

    /**
     * Test accessibility log success tracking.
     */
    public function test_accessibility_log_success_tracking(): void
    {
        $successLog = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'action' => 'feature_used',
            'success' => true,
            'response_time' => 100,
        ]);

        $failedLog = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'action' => 'feature_used',
            'success' => false,
            'response_time' => 500,
        ]);

        $this->assertTrue($successLog->success);
        $this->assertFalse($failedLog->success);
        $this->assertEquals(100, $successLog->response_time);
        $this->assertEquals(500, $failedLog->response_time);
    }

    /**
     * Test accessibility log response times.
     */
    public function test_accessibility_log_response_times(): void
    {
        $fastLog = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'response_time' => 50,
        ]);

        $slowLog = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'response_time' => 300,
        ]);

        $this->assertEquals(50, $fastLog->response_time);
        $this->assertEquals(300, $slowLog->response_time);
    }

    /**
     * Test accessibility log languages.
     */
    public function test_accessibility_log_languages(): void
    {
        $languages = ['es-CO', 'en-US', 'pt-BR', 'fr-FR'];

        foreach ($languages as $language) {
            $log = AccessibilityLog::factory()->create([
                'user_id' => $this->user->id,
                'language' => $language,
                'action' => 'language_changed',
                'success' => true,
            ]);

            $this->assertEquals($language, $log->language);
            $this->assertEquals('language_changed', $log->action);
        }
    }

    /**
     * Test accessibility log modes.
     */
    public function test_accessibility_log_modes(): void
    {
        $modes = ['standard', 'screen_reader', 'voice_control', 'sign_language', 'high_contrast', 'large_text'];

        foreach ($modes as $mode) {
            $log = AccessibilityLog::factory()->create([
                'user_id' => $this->user->id,
                'accessibility_mode' => $mode,
                'action' => 'mode_switched',
                'success' => true,
            ]);

            $this->assertEquals($mode, $log->accessibility_mode);
            $this->assertEquals('mode_switched', $log->action);
        }
    }

    /**
     * Test accessibility log locations.
     */
    public function test_accessibility_log_locations(): void
    {
        $locations = ['Bogotá', 'Medellín', 'Cali', 'Cartagena'];

        foreach ($locations as $location) {
            $log = AccessibilityLog::factory()->create([
                'user_id' => $this->user->id,
                'location' => $location,
                'action' => 'location_changed',
                'success' => true,
            ]);

            $this->assertEquals($location, $log->location);
            $this->assertEquals('location_changed', $log->action);
        }
    }

    /**
     * Test accessibility log timezones.
     */
    public function test_accessibility_log_timezones(): void
    {
        $timezones = ['America/Bogota', 'America/Mexico_City', 'Europe/Madrid', 'Asia/Tokyo'];

        foreach ($timezones as $timezone) {
            $log = AccessibilityLog::factory()->create([
                'user_id' => $this->user->id,
                'timezone' => $timezone,
                'action' => 'timezone_changed',
                'success' => true,
            ]);

            $this->assertEquals($timezone, $log->timezone);
            $this->assertEquals('timezone_changed', $log->action);
        }
    }

    /**
     * Test accessibility log metadata.
     */
    public function test_accessibility_log_can_have_metadata(): void
    {
        $metadata = [
            'device_info' => [
                'screen_resolution' => '1920x1080',
                'browser_version' => 'Chrome 91.0',
            ],
            'feature_settings' => [
                'voice_commands_enabled' => true,
                'high_contrast_level' => 'medium',
                'font_size_multiplier' => 1.5,
            ],
            'user_preferences' => [
                'preferred_language' => 'es-CO',
                'auto_translate' => true,
                'haptic_feedback' => true,
            ],
            'technical_details' => [
                'api_version' => 'v1.0',
                'sdk_version' => '2.1.0',
                'network_speed' => '50Mbps',
            ],
        ];

        $log = AccessibilityLog::factory()->create([
            'metadata' => $metadata,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($metadata, $log->metadata);
    }

    /**
     * Test accessibility log scope methods.
     */
    public function test_accessibility_log_scopes(): void
    {
        // Test byUser scope
        $userLogs = AccessibilityLog::byUser($this->user->id)->get();
        foreach ($userLogs as $log) {
            $this->assertEquals($this->user->id, $log->user->id);
        }

        // Test byFeature scope
        $screenReaderLogs = AccessibilityLog::byFeature('screen_reader')->get();
        foreach ($screenReaderLogs as $log) {
            $this->assertEquals('screen_reader', $log->feature);
        }

        // Test successful scope
        $successfulLogs = AccessibilityLog::successful()->get();
        foreach ($successfulLogs as $log) {
            $this->assertTrue($log->success);
        }

        // Test failed scope
        $failedLogs = AccessibilityLog::failed()->get();
        foreach ($failedLogs as $log) {
            $this->assertFalse($log->success);
        }

        // Test byDeviceType scope
        $mobileLogs = AccessibilityLog::byDeviceType('mobile')->get();
        foreach ($mobileLogs as $log) {
            $this->assertEquals('mobile', $log->device_type);
        }

        // Test byDate scope
        $todayLogs = AccessibilityLog::byDate(now()->toDateString())->get();
        foreach ($todayLogs as $log) {
            $this->assertEquals(now()->toDateString(), $log->created_at->toDateString());
        }

        // Test byAction scope
        $loginLogs = AccessibilityLog::byAction('login')->get();
        foreach ($loginLogs as $log) {
            $this->assertEquals('login', $log->action);
        }
    }

    /**
     * Test accessibility log static methods.
     */
    public function test_accessibility_log_static_methods(): void
    {
        // Test logFeatureUse method
        $logData = [
            'user_id' => $this->user->id,
            'feature' => 'voice_commands',
            'action' => 'used',
            'success' => true,
        ];

        AccessibilityLog::logFeatureUse($logData);
        $this->assertDatabaseHas('accessibility_logs', $logData);

        // Test logAccessibilityError method
        $errorData = [
            'user_id' => $this->user->id,
            'feature' => 'screen_reader',
            'action' => 'failed',
            'error_code' => 'SCREEN_READER_ERROR',
            'error_message' => 'Screen reader failed to initialize',
        ];

        AccessibilityLog::logAccessibilityError($errorData);
        $this->assertDatabaseHas('accessibility_logs', [
            'user_id' => $this->user->id,
            'feature' => 'screen_reader',
            'action' => 'failed',
            'error_code' => 'SCREEN_READER_ERROR',
        ]);

        // Test getUsageStatistics method
        $stats = AccessibilityLog::getUsageStatistics($this->user->id);
        $this->assertArrayHasKey('total_logs', $stats);
        $this->assertArrayHasKey('successful_logs', $stats);
        $this->assertArrayHasKey('success_rate', $stats);
        $this->assertArrayHasKey('most_used_features', $stats);
        $this->assertArrayHasKey('average_response_time', $stats);

        // Test getFeatureUsageByPeriod method
        $usageData = AccessibilityLog::getFeatureUsageByPeriod('screen_reader', now()->subDays(30));
        $this->assertArrayHasKey('period', $usageData);
        $this->assertArrayHasKey('data', $usageData);
        $this->assertEquals('screen_reader', $usageData['feature']);
        $this->assertEquals('30_days', $usageData['period']);

        // Test getDeviceUsageByPeriod method
        $deviceUsageData = AccessibilityLog::getDeviceUsageByPeriod('mobile', now()->subDays(30));
        $this->assertArrayHasKey('period', $deviceUsageData);
        $this->assertArrayHasKey('data', $deviceUsageData);
        $this->assertEquals('mobile', $deviceUsageData['device_type']);
        $this->assertEquals('30_days', $deviceUsageData['period']);

        // Test getSuccessRate method
        $successRate = AccessibilityLog::getSuccessRate($this->user->id);
        $this->assertIsFloat($successRate);
        $this->assertGreaterThanOrEqual(0, $successRate);
        $this->assertLessThanOrEqual(100, $successRate);

        // Test getMostUsedFeatures method
        $mostUsed = AccessibilityLog::getMostUsedFeatures($this->user->id);
        $this->assertIsArray($mostUsed);
        $this->assertArrayHasKey('screen_reader', $mostUsed);
        $this->assertArrayHasKey('voice_commands', $mostUsed);
    }

    /**
     * Test accessibility log error handling.
     */
    public function test_accessibility_log_error_handling(): void
    {
        $log = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'feature' => 'high_contrast',
            'action' => 'failed',
            'error_code' => 'CONTRAST_ERROR',
            'error_message' => 'High contrast mode failed',
        ]);

        $this->assertDatabaseHas('accessibility_logs', [
            'user_id' => $this->user->id,
            'feature' => 'high_contrast',
            'action' => 'failed',
            'error_code' => 'CONTRAST_ERROR',
        ]);
    }

    /**
     * Test accessibility log performance tracking.
     */
    public function test_accessibility_log_performance_tracking(): void
    {
        $performanceLog = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'action' => 'performance_test',
            'response_time' => 75,
            'metadata' => [
                'memory_usage' => '256MB',
                'cpu_usage' => '15%',
                'network_latency' => '25ms',
            ],
        ]);

        $this->assertEquals(75, $performanceLog->response_time);
        $this->assertEquals('256MB', $performanceLog->metadata['memory_usage']);
        $this->assertEquals('15%', $performanceLog->metadata['cpu_usage']);
        $this->assertEquals('25ms', $performanceLog->metadata['network_latency']);
    }

    /**
     * Test accessibility log user preferences.
     */
    public function test_accessibility_log_user_preferences(): void
    {
        $log = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'action' => 'preferences_updated',
            'metadata' => [
                'preferred_language' => 'es-CO',
                'preferred_contrast' => 'high',
                'preferred_font_size' => 'large',
                'auto_voice_commands' => true,
            ],
        ]);

        $this->assertEquals('preferences_updated', $log->action);
        $this->assertEquals('es-CO', $log->metadata['preferred_language']);
        $this->assertEquals('high', $log->metadata['preferred_contrast']);
        $this->assertEquals('large', $log->metadata['preferred_font_size']);
        $this->assertTrue($log->metadata['auto_voice_commands']);
    }

    /**
     * Test accessibility log accessibility testing.
     */
    public function test_accessibility_log_accessibility_testing(): void
    {
        $log = AccessibilityLog::factory()->create([
            'user_id' => $this->user->id,
            'feature' => 'accessibility_test',
            'action' => 'test_completed',
            'success' => true,
            'metadata' => [
                'test_results' => [
                    'screen_reader' => 'passed',
                    'voice_commands' => 'passed',
                    'high_contrast' => 'passed',
                    'large_text' => 'failed',
                ],
                'test_environment' => [
                    'device' => 'iPhone 13',
                    'os_version' => 'iOS 16.0',
                    'browser' => 'Safari',
                ],
            ],
        ]);

        $this->assertEquals('accessibility_test', $log->feature);
        $this->assertEquals('test_completed', $log->action);
        $this->assertEquals('passed', $log->metadata['test_results']['screen_reader']);
        $this->assertEquals('passed', $log->metadata['test_results']['voice_commands']);
        $this->assertEquals('passed', $log->metadata['test_results']['high_contrast']);
        $this->assertEquals('failed', $log->metadata['test_results']['large_text']);
    }
}
