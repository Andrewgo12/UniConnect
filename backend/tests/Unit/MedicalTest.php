<?php

namespace Tests\Unit;

use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MedicalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $medicalRecord;
    protected $medication;
    protected $appointment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'accessibility_needs' => ['blind', 'deaf'],
        ]);
        
        $this->medicalRecord = MedicalRecord::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Medical Record',
            'type' => 'diagnosis',
            'diagnosis' => 'Test diagnosis',
            'created_by' => $this->user->id,
        ]);
        
        $this->medication = Medication::factory()->create([
            'user_id' => $this->user->id,
            'medical_record_id' => $this->medicalRecord->id,
            'name' => 'Test Medication',
            'dosage' => '10mg',
            'frequency' => 'daily',
            'created_by' => $this->user->id,
        ]);
        
        $this->appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'medical_record_id' => $this->medicalRecord->id,
            'title' => 'Test Appointment',
            'type' => 'consultation',
            'status' => 'scheduled',
            'scheduled_date' => now()->addDays(7),
            'created_by' => $this->user->id,
        ]);
    }

    /**
     * Test medical record creation.
     */
    public function test_medical_record_can_be_created(): void
    {
        $recordData = [
            'user_id' => $this->user->id,
            'title' => 'New Medical Record',
            'type' => 'treatment',
            'diagnosis' => 'New diagnosis',
            'treatment' => 'New treatment plan',
            'notes' => 'Medical notes',
            'created_by' => $this->user->id,
        ];

        $record = MedicalRecord::create($recordData);

        $this->assertDatabaseHas('medical_records', $recordData);
        $this->assertEquals('New Medical Record', $record->title);
        $this->assertEquals('treatment', $record->type);
    }

    /**
     * Test medical record user relationship.
     */
    public function test_medical_record_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->medicalRecord->user);
        $this->assertEquals($this->user->id, $this->medicalRecord->user->id);
    }

    /**
     * Test medical record medications relationship.
     */
    public function test_medical_record_can_have_medications(): void
    {
        $medications = Medication::factory()->count(3)->create([
            'medical_record_id' => $this->medicalRecord->id,
        ]);

        $this->assertCount(4, $this->medicalRecord->prescribedMedications);
    }

    /**
     * Test medical record appointments relationship.
     */
    public function test_medical_record_can_have_appointments(): void
    {
        $appointments = Appointment::factory()->count(2)->create([
            'medical_record_id' => $this->medicalRecord->id,
        ]);

        $this->assertCount(3, $this->medicalRecord->appointments);
    }

    /**
     * Test medical record types.
     */
    public function test_medical_record_types(): void
    {
        $types = ['diagnosis', 'treatment', 'lab_result', 'prescription', 'vaccination', 'allergy_test'];

        foreach ($types as $type) {
            $record = MedicalRecord::factory()->create([
                'type' => $type,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($type, $record->type);
        }
    }

    /**
     * Test medical record accessibility data.
     */
    public function test_medical_record_can_have_accessibility_data(): void
    {
        $accessibilityData = [
            'screen_reader_optimized' => true,
            'voice_commands' => true,
            'large_text' => true,
            'high_contrast' => false,
        ];

        $record = MedicalRecord::factory()->create([
            'accessibility_data' => $accessibilityData,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($accessibilityData, $record->accessibility_data);
    }

    /**
     * Test medication creation.
     */
    public function test_medication_can_be_created(): void
    {
        $medicationData = [
            'user_id' => $this->user->id,
            'medical_record_id' => $this->medicalRecord->id,
            'name' => 'Test Medication',
            'dosage' => '50mg',
            'frequency' => 'twice_daily',
            'route' => 'oral',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'created_by' => $this->user->id,
        ];

        $medication = Medication::create($medicationData);

        $this->assertDatabaseHas('medications', [
            'user_id' => $this->user->id,
            'medical_record_id' => $this->medicalRecord->id,
            'name' => 'Test Medication',
            'dosage' => '50mg',
            'frequency' => 'twice_daily',
            'route' => 'oral',
            'created_by' => $this->user->id,
        ]);
        $this->assertEquals('Test Medication', $medication->name);
        $this->assertEquals('50mg', $medication->dosage);
    }

    /**
     * Test medication medical record relationship.
     */
    public function test_medication_belongs_to_medical_record(): void
    {
        $this->assertInstanceOf(MedicalRecord::class, $this->medication->medicalRecord);
        $this->assertEquals($this->medicalRecord->id, $this->medication->medical_record_id);
    }

    /**
     * Test medication user relationship.
     */
    public function test_medication_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->medication->user);
        $this->assertEquals($this->user->id, $this->medication->user->id);
    }

    /**
     * Test medication interactions.
     */
    public function test_medication_can_have_interactions(): void
    {
        $interactions = ['Aspirin', 'Ibuprofen', 'Warfarin'];

        $medication = Medication::factory()->create([
            'interactions' => $interactions,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($interactions, $medication->interactions);
        $this->assertIsArray($medication->interactions);
    }

    /**
     * Test appointment creation.
     */
    public function test_appointment_can_be_created(): void
    {
        $appointmentData = [
            'user_id' => $this->user->id,
            'medical_record_id' => $this->medicalRecord->id,
            'title' => 'Test Appointment',
            'type' => 'follow_up',
            'appointment_date' => now()->addDays(14),
            'duration' => 60,
            'location' => 'Medical Center',
            'is_virtual' => false,
            'status' => 'scheduled',
            'created_by' => $this->user->id,
        ];

        $appointment = Appointment::create($appointmentData);

        $this->assertDatabaseHas('appointments', $appointmentData);
        $this->assertEquals('Test Appointment', $appointment->title);
        $this->assertEquals('follow_up', $appointment->type);
        $this->assertFalse($appointment->is_virtual);
    }

    /**
     * Test appointment medical record relationship.
     */
    public function test_appointment_belongs_to_medical_record(): void
    {
        $this->assertInstanceOf(MedicalRecord::class, $this->appointment->medicalRecord);
        $this->assertEquals($this->medicalRecord->id, $this->appointment->medical_record_id);
    }

    /**
     * Test appointment user relationship.
     */
    public function test_appointment_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->appointment->user);
        $this->assertEquals($this->user->id, $this->appointment->user->id);
    }

    /**
     * Test appointment types.
     */
    public function test_appointment_types(): void
    {
        $types = ['consultation', 'follow_up', 'procedure', 'emergency', 'virtual_consultation'];

        foreach ($types as $type) {
            $appointment = Appointment::factory()->create([
                'type' => $type,
                'user_id' => $this->user->id,
            ]);

            $this->assertEquals($type, $appointment->type);
        }
    }

    /**
     * Test appointment status transitions.
     */
    public function test_appointment_status_transitions(): void
    {
        $appointment = Appointment::factory()->create([
            'status' => 'scheduled',
            'user_id' => $this->user->id,
        ]);

        // Confirm appointment
        $appointment->update(['status' => 'confirmed']);
        $this->assertEquals('confirmed', $appointment->status);

        // Complete appointment
        $appointment->update(['status' => 'completed', 'completed_at' => now()]);
        $this->assertEquals('completed', $appointment->status);
        $this->assertNotNull($appointment->completed_at);

        // Cancel appointment
        $appointment->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        $this->assertEquals('cancelled', $appointment->status);
        $this->assertNotNull($appointment->cancelled_at);
    }

    /**
     * Test appointment virtual/physical types.
     */
    public function test_appointment_virtual_physical_types(): void
    {
        $virtualAppointment = Appointment::factory()->create([
            'title' => 'Virtual Appointment',
            'is_virtual' => true,
            'meeting_link' => 'https://zoom.us/j/123456789',
            'user_id' => $this->user->id,
        ]);

        $physicalAppointment = Appointment::factory()->create([
            'title' => 'Physical Appointment',
            'is_virtual' => false,
            'location' => 'Hospital Room 101',
            'user_id' => $this->user->id,
        ]);

        $this->assertTrue($virtualAppointment->is_virtual);
        $this->assertFalse($physicalAppointment->is_virtual);
        $this->assertNotNull($virtualAppointment->meeting_link);
        $this->assertNotNull($physicalAppointment->location);
    }

    /**
     * Test appointment reminders.
     */
    public function test_appointment_can_have_reminders(): void
    {
        $reminders = [
            [
                'type' => 'email',
                'time_before' => 24, // hours
                'sent' => true,
            ],
            [
                'type' => 'sms',
                'time_before' => 2, // hours
                'sent' => false,
            ],
        ];

        $appointment = Appointment::factory()->create([
            'reminders' => $reminders,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($reminders, $appointment->reminders);
        $this->assertIsArray($appointment->reminders);
    }

    /**
     * Test medical record scope methods.
     */
    public function test_medical_record_scopes(): void
    {
        // Test byType scope
        $diagnosisRecords = MedicalRecord::byType('diagnosis')->get();
        foreach ($diagnosisRecords as $record) {
            $this->assertEquals('diagnosis', $record->type);
        }

        // Test byUser scope
        $userRecords = MedicalRecord::byUser($this->user->id)->get();
        foreach ($userRecords as $record) {
            $this->assertEquals($this->user->id, $record->user_id);
        }

        // Test recent scope
        $recentRecords = MedicalRecord::recent()->get();
        foreach ($recentRecords as $record) {
            $this->assertTrue($record->created_at->gt(now()->subDays(30)));
        }
    }

    /**
     * Test medication scope methods.
     */
    public function test_medication_scopes(): void
    {
        // Test active scope
        $activeMedications = Medication::active()->get();
        foreach ($activeMedications as $medication) {
            $this->assertEquals('active', $medication->status);
        }

        // Test byUser scope
        $userMedications = Medication::byUser($this->user->id)->get();
        foreach ($userMedications as $medication) {
            $this->assertEquals($this->user->id, $medication->user_id);
        }

        // Test expiringSoon scope
        $expiringMedications = Medication::expiringSoon()->get();
        foreach ($expiringMedications as $medication) {
            $this->assertTrue($medication->end_date->between(now(), now()->addDays(30)));
        }
    }

    /**
     * Test appointment scope methods.
     */
    public function test_appointment_scopes(): void
    {
        // Test upcoming scope
        $upcomingAppointments = Appointment::upcoming()->get();
        foreach ($upcomingAppointments as $appointment) {
            $date = $appointment->scheduled_date ?? $appointment->appointment_date;
            $this->assertNotNull($date, 'Upcoming appointment must have scheduled_date or appointment_date');
            $this->assertTrue($date->gt(now()));
        }

        // Test byUser scope
        $userAppointments = Appointment::byUser($this->user->id)->get();
        foreach ($userAppointments as $appointment) {
            $this->assertEquals($this->user->id, $appointment->user_id);
        }

        // Test virtual scope
        $virtualAppointments = Appointment::virtual()->get();
        foreach ($virtualAppointments as $appointment) {
            $this->assertTrue($appointment->is_virtual);
        }

        // Test byStatus scope
        $scheduledAppointments = Appointment::byStatus('scheduled')->get();
        foreach ($scheduledAppointments as $appointment) {
            $this->assertEquals('scheduled', $appointment->status);
        }
    }

    /**
     * Test medical record metadata.
     */
    public function test_medical_record_can_have_metadata(): void
    {
        $metadata = [
            'accessibility_features' => ['voice_commands', 'screen_reader'],
            'technical_specs' => [
                'hipaa_compliant' => true,
                'encryption_level' => 'AES-256',
            ],
            'integration_data' => [
                'external_system_id' => 'ext_123',
                'sync_enabled' => true,
            ],
        ];

        $record = MedicalRecord::factory()->create([
            'metadata' => $metadata,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($metadata, $record->metadata);
    }
}
