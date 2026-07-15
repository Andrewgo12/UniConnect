<?php

namespace Tests\Unit;

use App\Models\Emergency;
use App\Models\User;
use App\Http\Resources\EmergencyResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class EmergencyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $emergency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'accessibility_needs' => ['blind', 'deaf'],
        ]);
        
        $this->emergency = Emergency::factory()->create([
            'type' => 'medical',
            'severity' => 'high',
            'description' => 'Test emergency description',
            'location' => 'Test Location',
            'latitude' => 4.6097,
            'longitude' => -74.0817,
            'contact_name' => 'Emergency Contact',
            'contact_phone' => '+1234567890',
            'contact_relationship' => 'family',
            'medical_conditions' => ['diabetes', 'hypertension'],
            'accessibility_needs' => ['blind', 'deaf'],
            'user_id' => $this->user->id,
        ]);
        $this->emergency->load('user');
    }

    /**
     * Test emergency creation.
     */
    public function test_emergency_can_be_created(): void
    {
        $emergencyData = [
            'type' => 'accident',
            'severity' => 'critical',
            'description' => 'Critical emergency description',
            'location' => 'Emergency Location',
            'latitude' => 4.6097,
            'longitude' => -74.0817,
            'contact_name' => 'Emergency Contact',
            'contact_phone' => '+1234567890',
            'contact_relationship' => 'family',
            'medical_conditions' => ['asthma'],
            'accessibility_needs' => ['blind'],
            'user_id' => $this->user->id,
        ];

        $emergency = Emergency::create($emergencyData);

        $this->assertDatabaseHas('emergencies', [
            'type' => 'accident',
            'severity' => 'critical',
            'description' => 'Critical emergency description',
            'location' => 'Emergency Location',
            'contact_name' => 'Emergency Contact',
            'contact_phone' => '+1234567890',
            'contact_relationship' => 'family',
            'user_id' => $this->user->id,
        ]);
        $this->assertEquals('accident', $emergency->type);
        $this->assertEquals('critical', $emergency->severity);
        $this->assertEquals('Emergency Location', $emergency->location);
        $this->assertEqualsWithDelta(4.6097, (float) $emergency->latitude, 0.0001);
        $this->assertEqualsWithDelta(-74.0817, (float) $emergency->longitude, 0.0001);
        $this->assertEquals(['asthma'], $emergency->medical_conditions);
        $this->assertEquals(['blind'], $emergency->accessibility_needs);
    }

    /**
     * Test emergency user relationship.
     */
    public function test_emergency_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->emergency->user);
        $this->assertEquals($this->user->id, $this->emergency->user->id);
    }

    /**
     * Test emergency types.
     */
    public function test_emergency_types(): void
    {
        $types = ['medical', 'accident', 'violence', 'natural_disaster', 'technical', 'other'];

        foreach ($types as $type) {
            $emergency = Emergency::factory()->create([
                'type' => $type,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($type, $emergency->type);
        }
    }

    /**
     * Test emergency severity levels.
     */
    public function test_emergency_severity_levels(): void
    {
        $severities = ['low', 'medium', 'high', 'critical'];

        foreach ($severities as $severity) {
            $emergency = Emergency::factory()->create([
                'severity' => $severity,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($severity, $emergency->severity);
        }
    }

    /**
     * Test emergency location data.
     */
    public function test_emergency_location_data(): void
    {
        $emergency = Emergency::factory()->create([
            'location' => '123 Test Street, City',
            'latitude' => 4.6097,
            'longitude' => -74.0817,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('123 Test Street, City', $emergency->location);
        $this->assertEquals(4.6097, $emergency->latitude);
        $this->assertEquals(-74.0817, $emergency->longitude);
    }

    /**
     * Test emergency contact information.
     */
    public function test_emergency_contact_information(): void
    {
        $emergency = Emergency::factory()->create([
            'contact_name' => 'John Doe',
            'contact_phone' => '+1234567890',
            'contact_relationship' => 'spouse',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('John Doe', $emergency->contact_name);
        $this->assertEquals('+1234567890', $emergency->contact_phone);
        $this->assertEquals('spouse', $emergency->contact_relationship);
    }

    /**
     * Test emergency medical conditions.
     */
    public function test_emergency_medical_conditions(): void
    {
        $conditions = ['diabetes', 'hypertension', 'asthma', 'heart_disease', 'epilepsy'];

        $emergency = Emergency::factory()->create([
            'medical_conditions' => $conditions,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($conditions, $emergency->medical_conditions);
        $this->assertIsArray($emergency->medical_conditions);
    }

    /**
     * Test emergency accessibility needs.
     */
    public function test_emergency_accessibility_needs(): void
    {
        $needs = ['blind', 'deaf', 'mute', 'mobility_impaired', 'cognitive_disability'];

        $emergency = Emergency::factory()->create([
            'accessibility_needs' => $needs,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($needs, $emergency->accessibility_needs);
        $this->assertIsArray($emergency->accessibility_needs);
    }

    /**
     * Test emergency status transitions.
     */
    public function test_emergency_status_transitions(): void
    {
        $emergency = Emergency::factory()->create([
            'status' => 'active',
            'user_id' => $this->user->id,
        ]);

        // Acknowledge emergency
        $emergency->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
        ]);

        $this->assertEquals('acknowledged', $emergency->status);
        $this->assertNotNull($emergency->acknowledged_at);

        // Resolve emergency
        $emergency->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        $this->assertEquals('resolved', $emergency->status);
        $this->assertNotNull($emergency->resolved_at);
    }

    /**
     * Test emergency metadata.
     */
    public function test_emergency_can_have_metadata(): void
    {
        $metadata = [
            'response_time' => 15, // minutes
            'responder_type' => 'medical',
            'additional_info' => [
                'weather' => 'rainy',
                'traffic' => 'heavy',
            ],
            'accessibility_features_used' => [
                'voice_commands' => true,
                'screen_reader' => true,
            ],
        ];

        $emergency = Emergency::factory()->create([
            'metadata' => $metadata,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($metadata, $emergency->metadata);
    }

    /**
     * Test emergency resource transformation.
     */
    public function test_emergency_resource_transformation(): void
    {
        $resource = new EmergencyResource($this->emergency);

        $this->assertEquals($this->emergency->id, $resource['id']);
        $this->assertEquals($this->emergency->type, $resource['type']);
        $this->assertEquals($this->emergency->severity, $resource['severity']);
        $this->assertEquals($this->emergency->description, $resource['description']);
        $this->assertEquals($this->emergency->location, $resource['location']);
        $this->assertArrayHasKey('user', $resource->toArray(Request::create('/')));
    }

    /**
     * Test emergency scope methods.
     */
    public function test_emergency_scopes(): void
    {
        // Test byType scope
        $medicalEmergencies = Emergency::byType('medical')->get();
        foreach ($medicalEmergencies as $emergency) {
            $this->assertEquals('medical', $emergency->type);
        }

        // Test bySeverity scope
        $highSeverityEmergencies = Emergency::bySeverity('high')->get();
        foreach ($highSeverityEmergencies as $emergency) {
            $this->assertEquals('high', $emergency->severity);
        }

        // Test active scope
        $activeEmergencies = Emergency::active()->get();
        foreach ($activeEmergencies as $emergency) {
            $this->assertEquals('active', $emergency->status);
        }

        // Test resolved scope
        $resolvedEmergencies = Emergency::resolved()->get();
        foreach ($resolvedEmergencies as $emergency) {
            $this->assertEquals('resolved', $emergency->status);
        }

        // Test byUser scope
        $userEmergencies = Emergency::byUser($this->user->id)->get();
        foreach ($userEmergencies as $emergency) {
            $this->assertEquals($this->user->id, $emergency->user->id);
        }
    }

    /**
     * Test emergency with media files.
     */
    public function test_emergency_can_have_media(): void
    {
        $emergency = Emergency::factory()->create([
            'description' => 'Emergency with media',
            'user_id' => $this->user->id,
        ]);

        // Simulate media attachment
        $emergency->addMediaFromRequest('media_files', [
            'emergency_photo.jpg',
            'medical_document.pdf',
        ]);

        $this->assertGreaterThan(0, $emergency->media->count());
    }

    /**
     * Test emergency response time tracking.
     */
    public function test_emergency_response_time(): void
    {
        $startTime = now()->subMinutes(30);
        
        $emergency = Emergency::factory()->create([
            'status' => 'active',
            'created_at' => $startTime,
            'user_id' => $this->user->id,
        ]);

        $acknowledgedAt = now();
        $emergency->update(['acknowledged_at' => $acknowledgedAt]);

        $responseTime = $emergency->response_time;
        $this->assertEquals(30, $responseTime);
    }

    /**
     * Test emergency priority handling.
     */
    public function test_emergency_priority_handling(): void
    {
        $criticalEmergency = Emergency::factory()->create([
            'type' => 'medical',
            'severity' => 'critical',
            'user_id' => $this->user->id,
        ]);

        $lowPriorityEmergency = Emergency::factory()->create([
            'type' => 'accident',
            'severity' => 'low',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('critical', $criticalEmergency->severity);
        $this->assertEquals('low', $lowPriorityEmergency->severity);
    }

    /**
     * Test emergency accessibility integration.
     */
    public function test_emergency_accessibility_integration(): void
    {
        $accessibleEmergency = Emergency::factory()->create([
            'accessibility_needs' => ['blind', 'deaf'],
            'user_id' => $this->user->id,
        ]);

        $this->assertContains('blind', $accessibleEmergency->accessibility_needs);
        $this->assertContains('deaf', $accessibleEmergency->accessibility_needs);
    }
}
