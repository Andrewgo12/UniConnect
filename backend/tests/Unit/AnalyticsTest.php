<?php

namespace Tests\Unit;

use App\Models\Analytics;
use App\Models\User;
use App\Http\Resources\AnalyticsResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $analytics;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'accessibility_needs' => ['blind', 'deaf'],
        ]);
        
        $this->analytics = Analytics::factory()->create([
            'user_id' => $this->user->id,
            'event_type' => 'user_action',
            'category' => 'engagement',
            'action' => 'message_sent',
            'resource_type' => 'message',
            'resource_id' => 123,
            'value' => 1,
            'metadata' => ['test_data' => 'value'],
        ]);
    }

    /**
     * Test analytics creation.
     */
    public function test_analytics_can_be_created(): void
    {
        $analyticsData = [
            'user_id' => $this->user->id,
            'event_type' => 'user_action',
            'category' => 'engagement',
            'action' => 'user_registered',
            'resource_type' => 'user',
            'resource_id' => $this->user->id,
            'value' => 1,
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Test Browser',
            'device_type' => 'desktop',
            'platform' => 'windows',
            'language' => 'es-CO',
            'accessibility_mode' => 'screen_reader',
            'response_time' => 250,
            'success' => true,
            'location' => 'Bogotá',
            'timezone' => 'America/Bogota',
        ];

        $analytics = Analytics::create($analyticsData);

        $this->assertDatabaseHas('analytics', $analyticsData);
        $this->assertEquals('user_action', $analytics->event_type);
        $this->assertEquals('engagement', $analytics->category);
        $this->assertEquals('user_registered', $analytics->action);
        $this->assertEquals(true, $analytics->success);
    }

    /**
     * Test analytics user relationship.
     */
    public function test_analytics_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->analytics->user);
        $this->assertEquals($this->user->id, $this->analytics->user->id);
    }

    /**
     * Test analytics event types.
     */
    public function test_analytics_event_types(): void
    {
        $eventTypes = ['user_action', 'system_event', 'emergency_event', 'security_alert', 'performance_metric'];

        foreach ($eventTypes as $eventType) {
            $analytics = Analytics::factory()->create([
                'event_type' => $eventType,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($eventType, $analytics->event_type);
        }
    }

    /**
     * Test analytics categories.
     */
    public function test_analytics_categories(): void
    {
        $categories = ['engagement', 'accessibility', 'security', 'performance', 'usage', 'error'];

        foreach ($categories as $category) {
            $analytics = Analytics::factory()->create([
                'category' => $category,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($category, $analytics->category);
        }
    }

    /**
     * Test analytics actions.
     */
    public function test_analytics_actions(): void
    {
        $actions = ['user_registered', 'user_login', 'user_logout', 'message_sent', 'emergency_triggered', 'security_alert', 'file_uploaded', 'file_downloaded'];

        foreach ($actions as $action) {
            $analytics = Analytics::factory()->create([
                'action' => $action,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($action, $analytics->action);
        }
    }

    /**
     * Test analytics resource types.
     */
    public function test_analytics_resource_types(): void
    {
        $resourceTypes = ['user', 'message', 'conversation', 'emergency', 'medical_record', 'sign_language', 'audio', 'image', 'system'];

        foreach ($resourceTypes as $resourceType) {
            $analytics = Analytics::factory()->create([
                'resource_type' => $resourceType,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($resourceType, $analytics->resource_type);
        }
    }

    /**
     * Test analytics device types.
     */
    public function test_analytics_device_types(): void
    {
        $deviceTypes = ['desktop', 'mobile', 'tablet', 'voice_assistant'];

        foreach ($deviceTypes as $deviceType) {
            $analytics = Analytics::factory()->create([
                'device_type' => $deviceType,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($deviceType, $analytics->device_type);
        }
    }

    /**
     * Test analytics platforms.
     */
    public function test_analytics_platforms(): void
    {
        $platforms = ['windows', 'macos', 'linux', 'ios', 'android'];

        foreach ($platforms as $platform) {
            $analytics = Analytics::factory()->create([
                'platform' => $platform,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($platform, $analytics->platform);
        }
    }

    /**
     * Test analytics accessibility modes.
     */
    public function test_analytics_accessibility_modes(): void
    {
        $modes = ['standard', 'screen_reader', 'voice_control', 'sign_language', 'high_contrast', 'large_text'];

        foreach ($modes as $mode) {
            $analytics = Analytics::factory()->create([
                'accessibility_mode' => $mode,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($mode, $analytics->accessibility_mode);
        }
    }

    /**
     * Test analytics response times.
     */
    public function test_analytics_response_times(): void
    {
        $fastResponse = Analytics::factory()->create([
            'response_time' => 100,
            'user_id' => $this->user->id,
        ]);

        $slowResponse = Analytics::factory()->create([
            'response_time' => 2000,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals(100, $fastResponse->response_time);
        $this->assertEquals(2000, $slowResponse->response_time);
    }

    /**
     * Test analytics success tracking.
     */
    public function test_analytics_success_tracking(): void
    {
        $successEvent = Analytics::factory()->create([
            'success' => true,
            'user_id' => $this->user->id,
        ]);

        $failureEvent = Analytics::factory()->create([
            'success' => false,
            'error_code' => 'VALIDATION_ERROR',
            'user_id' => $this->user->id,
        ]);

        $this->assertTrue($successEvent->success);
        $this->assertFalse($failureEvent->success);
        $this->assertNull($successEvent->error_code);
        $this->assertEquals('VALIDATION_ERROR', $failureEvent->error_code);
    }

    /**
     * Test analytics error codes.
     */
    public function test_analytics_error_codes(): void
    {
        $errorCodes = ['VALIDATION_ERROR', 'AUTHENTICATION_ERROR', 'PERMISSION_ERROR', 'SYSTEM_ERROR', 'NETWORK_ERROR'];

        foreach ($errorCodes as $errorCode) {
            $analytics = Analytics::factory()->create([
                'error_code' => $errorCode,
                'success' => false,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($errorCode, $analytics->error_code);
            $this->assertFalse($analytics->success);
        }
    }

    /**
     * Test analytics location data.
     */
    public function test_analytics_location_data(): void
    {
        $locationData = [
            'city' => 'Bogotá',
            'country' => 'Colombia',
            'latitude' => 4.6097,
            'longitude' => -74.0817,
        ];

        $analytics = Analytics::factory()->create([
            'location' => json_encode($locationData),
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($locationData, json_decode($analytics->location, true));
    }

    /**
     * Test analytics metadata.
     */
    public function test_analytics_can_have_metadata(): void
    {
        $metadata = [
            'user_agent_details' => [
                'browser' => 'Chrome',
                'version' => '91.0.4472.124',
                'platform' => 'Windows 10',
            ],
            'session_info' => [
                'duration' => 3600, // 1 hour
                'page_views' => 15,
                'interactions' => 45,
            ],
            'accessibility_features' => [
                'screen_reader_used' => true,
                'voice_commands_used' => false,
                'high_contrast_enabled' => true,
            ],
            'technical_details' => [
                'memory_usage' => '512MB',
                'cpu_usage' => '25%',
                'network_speed' => '50Mbps',
            ],
        ];

        $analytics = Analytics::factory()->create([
            'metadata' => $metadata,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($metadata, $analytics->metadata);
    }

    /**
     * Test analytics resource transformation.
     */
    public function test_analytics_resource_transformation(): void
    {
        $resource = new AnalyticsResource($this->analytics);
        $req = Request::create('/');

        $this->assertEquals($this->analytics->id, $resource['id']);
        $this->assertEquals($this->analytics->event_type, $resource['event_type']);
        $this->assertEquals($this->analytics->category, $resource['category']);
        $this->assertEquals($this->analytics->action, $resource['action']);
        $this->assertEquals($this->analytics->resource_type, $resource['resource_type']);
        $this->assertEquals($this->analytics->value, $resource['value']);
        $this->assertArrayHasKey('user', $resource->toArray($req));
    }

    /**
     * Test analytics scope methods.
     */
    public function test_analytics_scopes(): void
    {
        // Test byEventType scope
        $userActions = Analytics::byEventType('user_action')->get();
        foreach ($userActions as $analytics) {
            $this->assertEquals('user_action', $analytics->event_type);
        }

        // Test byCategory scope
        $engagementEvents = Analytics::byCategory('engagement')->get();
        foreach ($engagementEvents as $analytics) {
            $this->assertEquals('engagement', $analytics->category);
        }

        // Test byUser scope
        $userAnalytics = Analytics::byUser($this->user->id)->get();
        foreach ($userAnalytics as $analytics) {
            $this->assertEquals($this->user->id, $analytics->user->id);
        }

        // Test successful scope
        $successfulEvents = Analytics::successful()->get();
        foreach ($successfulEvents as $analytics) {
            $this->assertTrue($analytics->success);
        }

        // Test failed scope
        $failedEvents = Analytics::failed()->get();
        foreach ($failedEvents as $analytics) {
            $this->assertFalse($analytics->success);
        }

        // Test byDate scope
        $todayEvents = Analytics::byDate(now()->toDateString())->get();
        foreach ($todayEvents as $analytics) {
            $this->assertEquals(now()->toDateString(), $analytics->created_at->toDateString());
        }
    }

    /**
     * Test analytics static methods.
     */
    public function test_analytics_static_methods(): void
    {
        // Test trackEvent method
        $eventData = [
            'user_id' => $this->user->id,
            'event_type' => 'test_event',
            'category' => 'test_category',
            'action' => 'test_action',
            'value' => 1,
        ];

        Analytics::trackEvent($eventData);
        $this->assertDatabaseHas('analytics', $eventData);

        // Test getStatistics method
        $stats = Analytics::getStatistics();
        $this->assertArrayHasKey('total_events', $stats);
        $this->assertArrayHasKey('successful_events', $stats);
        $this->assertArrayHasKey('failed_events', $stats);
        $this->assertArrayHasKey('success_rate', $stats);

        // Test getUsageByPeriod method
        $usageData = Analytics::getUsageByPeriod('daily', now()->subDays(30));
        $this->assertArrayHasKey('period', $usageData);
        $this->assertArrayHasKey('data', $usageData);
        $this->assertEquals('daily', $usageData['period']);

        // Test getPerformanceMetrics method
        $performanceData = Analytics::getPerformanceMetrics();
        $this->assertArrayHasKey('average_response_time', $performanceData);
        $this->assertArrayHasKey('peak_usage_hours', $performanceData);
        $this->assertArrayHasKey('system_uptime', $performanceData);

        // Test getAccessibilityStats method
        $accessibilityData = Analytics::getAccessibilityStats();
        $this->assertArrayHasKey('accessibility_modes', $accessibilityData);
        $this->assertArrayHasKey('feature_usage', $accessibilityData);
        $this->assertArrayHasKey('success_rate', $accessibilityData);
    }

    /**
     * Test analytics with multiple values.
     */
    public function test_analytics_can_track_multiple_values(): void
    {
        $analytics = Analytics::factory()->create([
            'value' => 100,
            'metadata' => [
                'multiple_values' => [1, 2, 3, 4, 5],
                'nested_data' => [
                    'level_1' => ['a', 'b', 'c'],
                    'level_2' => ['d', 'e', 'f'],
                ],
            ],
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals(100, $analytics->value);
        $this->assertArrayHasKey('multiple_values', $analytics->metadata);
        $this->assertArrayHasKey('nested_data', $analytics->metadata);
    }

    /**
     * Test analytics timezone handling.
     */
    public function test_analytics_timezone_handling(): void
    {
        $analytics = Analytics::factory()->create([
            'timezone' => 'America/Bogota',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('America/Bogota', $analytics->timezone);
    }

    /**
     * Test analytics language tracking.
     */
    public function test_analytics_language_tracking(): void
    {
        $spanishAnalytics = Analytics::factory()->create([
            'language' => 'es-CO',
            'user_id' => $this->user->id,
        ]);

        $englishAnalytics = Analytics::factory()->create([
            'language' => 'en-US',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('es-CO', $spanishAnalytics->language);
        $this->assertEquals('en-US', $englishAnalytics->language);
    }

    /**
     * Test analytics IP address tracking.
     */
    public function test_analytics_ip_tracking(): void
    {
        $analytics = Analytics::factory()->create([
            'ip_address' => '192.168.1.100',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('192.168.1.100', $analytics->ip_address);
    }

    /**
     * Test analytics user agent tracking.
     */
    public function test_analytics_user_agent_tracking(): void
    {
        $analytics = Analytics::factory()->create([
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', $analytics->user_agent);
    }
}
