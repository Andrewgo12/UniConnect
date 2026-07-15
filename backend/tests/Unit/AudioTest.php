<?php

namespace Tests\Unit;

use App\Models\Audio;
use App\Models\User;
use App\Http\Resources\AudioResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class AudioTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $audio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'accessibility_needs' => ['deaf', 'mute'],
        ]);
        
        $this->audio = Audio::factory()->create([
            'title' => 'Test Audio',
            'user_id' => $this->user->id,
            'description' => 'Test audio description',
            'type' => 'speech',
            'file_path' => 'uploads/test_audio.mp3',
            'original_name' => 'test_audio.mp3',
            'mime_type' => 'audio/mpeg',
            'size' => 1024000,
            'duration' => 120,
            'language' => 'es-CO',
            'quality' => 'medium',
            'created_by' => $this->user->id,
        ]);
    }

    /**
     * Test audio creation.
     */
    public function test_audio_can_be_created(): void
    {
        $audioData = [
            'title' => 'New Test Audio',
            'description' => 'New test description',
            'type' => 'voice_note',
            'file_path' => 'uploads/new_audio.mp3',
            'original_name' => 'new_audio.mp3',
            'mime_type' => 'audio/mpeg',
            'size' => 2048000,
            'duration' => 180,
            'language' => 'en-US',
            'quality' => 'high',
            'created_by' => $this->user->id,
        ];

        $audio = Audio::create($audioData);

        $this->assertDatabaseHas('audio', $audioData);
        $this->assertEquals('New Test Audio', $audio->title);
        $this->assertEquals('voice_note', $audio->type);
        $this->assertEquals(180, $audio->duration);
    }

    /**
     * Test audio user relationship.
     */
    public function test_audio_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->audio->user);
        $this->assertEquals($this->user->id, $this->audio->user->id);
    }

    /**
     * Test audio types.
     */
    public function test_audio_types(): void
    {
        $types = ['speech', 'voice_note', 'emergency', 'sign_language'];

        foreach ($types as $type) {
            $audio = Audio::factory()->create([
                'title' => "Test {$type} audio",
                'type' => $type,
            ]);

            $this->assertEquals($type, $audio->type);
        }
    }

    /**
     * Test audio quality levels.
     */
    public function test_audio_quality_levels(): void
    {
        $qualities = ['low', 'medium', 'high'];

        foreach ($qualities as $quality) {
            $audio = Audio::factory()->create([
                'title' => "Test {$quality} quality audio",
                'quality' => $quality,
            ]);

            $this->assertEquals($quality, $audio->quality);
        }
    }

    /**
     * Test audio language support.
     */
    public function test_audio_can_have_language(): void
    {
        $spanishAudio = Audio::factory()->create([
            'title' => 'Spanish Audio',
            'language' => 'es-CO',
        ]);

        $englishAudio = Audio::factory()->create([
            'title' => 'English Audio',
            'language' => 'en-US',
        ]);

        $this->assertEquals('es-CO', $spanishAudio->language);
        $this->assertEquals('en-US', $englishAudio->language);
    }

    /**
     * Test audio transcript.
     */
    public function test_audio_can_have_transcript(): void
    {
        $transcript = 'This is the transcript of the audio file';

        $audio = Audio::factory()->create([
            'title' => 'Audio with transcript',
            'transcript' => $transcript,
        ]);

        $this->assertEquals($transcript, $audio->transcript);
    }

    /**
     * Test audio processing status.
     */
    public function test_audio_processing_status(): void
    {
        $unprocessedAudio = Audio::factory()->create([
            'title' => 'Unprocessed Audio',
            'is_processed' => false,
        ]);

        $processedAudio = Audio::factory()->create([
            'title' => 'Processed Audio',
            'is_processed' => true,
        ]);

        $this->assertFalse($unprocessedAudio->is_processed);
        $this->assertTrue($processedAudio->is_processed);
    }

    /**
     * Test audio public status.
     */
    public function test_audio_public_status(): void
    {
        $publicAudio = Audio::factory()->create([
            'title' => 'Public Audio',
            'is_public' => true,
        ]);

        $privateAudio = Audio::factory()->create([
            'title' => 'Private Audio',
            'is_public' => false,
        ]);

        $this->assertTrue($publicAudio->is_public);
        $this->assertFalse($privateAudio->is_public);
    }

    /**
     * Test audio metadata.
     */
    public function test_audio_can_have_metadata(): void
    {
        $metadata = [
            'accessibility_features' => ['slow_playback', 'captions'],
            'technical_specs' => [
                'bitrate' => '192kbps',
                'sample_rate' => '44.1kHz',
            ],
            'usage_statistics' => [
                'play_count' => 50,
                'download_count' => 25,
            ],
        ];

        $audio = Audio::factory()->create([
            'title' => 'Audio with metadata',
            'metadata' => $metadata,
        ]);

        $this->assertEquals($metadata, $audio->metadata);
    }

    /**
     * Test audio duration formatting.
     */
    public function test_audio_duration_formatting(): void
    {
        $audio = Audio::factory()->create([
            'title' => 'Test Audio',
            'duration' => 125, // 2 minutes 5 seconds
        ]);

        $resource = new AudioResource($audio);
        $req = Request::create('/');
        $this->assertEquals('02:05', $resource->toArray($req)['formatted_duration']);
    }

    /**
     * Test audio size formatting.
     */
    public function test_audio_size_formatting(): void
    {
        $audio = Audio::factory()->create([
            'title' => 'Test Audio',
            'size' => 2097152, // 2MB
        ]);

        $resource = new AudioResource($audio);
        $req = Request::create('/');
        $this->assertEquals('2.00 MB', $resource->toArray($req)['formatted_size']);
    }

    /**
     * Test audio resource transformation.
     */
    public function test_audio_resource_transformation(): void
    {
        $resource = new AudioResource($this->audio);
        $req = Request::create('/');

        $this->assertEquals($this->audio->id, $resource['id']);
        $this->assertEquals($this->audio->title, $resource['title']);
        $this->assertEquals($this->audio->type, $resource['type']);
        $this->assertEquals($this->audio->duration, $resource['duration']);
        $this->assertEquals($this->audio->language, $resource['language']);
        $this->assertArrayHasKey('user', $resource->toArray($req));
    }

    /**
     * Test audio scope methods.
     */
    public function test_audio_scopes(): void
    {
        // Test byType scope
        $speechAudios = Audio::byType('speech')->get();
        foreach ($speechAudios as $audio) {
            $this->assertEquals('speech', $audio->type);
        }

        // Test processed scope
        $processedAudios = Audio::processed()->get();
        foreach ($processedAudios as $audio) {
            $this->assertTrue($audio->is_processed);
        }

        // Test public scope
        $publicAudios = Audio::public()->get();
        foreach ($publicAudios as $audio) {
            $this->assertTrue($audio->is_public);
        }

        // Test byUser scope
        $userAudios = Audio::byUser($this->user->id)->get();
        foreach ($userAudios as $audio) {
            $this->assertEquals($this->user->id, $audio->created_by);
        }
    }

    /**
     * Test audio with multiple transcripts.
     */
    public function test_audio_can_have_multiple_transcripts(): void
    {
        $audio = Audio::factory()->create([
            'title' => 'Audio with multiple transcripts',
        ]);

        $audio->addTranscript('es-CO', 'Transcripción en español');
        $audio->addTranscript('en-US', 'English transcript');

        $transcripts = $audio->transcripts;
        $this->assertCount(2, $transcripts);
        $this->assertArrayHasKey('es-CO', $transcripts);
        $this->assertArrayHasKey('en-US', $transcripts);
    }

    /**
     * Test audio accessibility optimization.
     */
    public function test_audio_accessibility_optimization(): void
    {
        $accessibleAudio = Audio::factory()->create([
            'title' => 'Accessible Audio',
            'type' => 'sign_language',
            'is_processed' => true,
        ]);

        $this->assertEquals('sign_language', $accessibleAudio->type);
        $this->assertTrue($accessibleAudio->is_processed);
    }

    /**
     * Test audio emergency processing.
     */
    public function test_audio_emergency_processing(): void
    {
        $emergencyAudio = Audio::factory()->create([
            'title' => 'Emergency Audio',
            'type' => 'emergency',
            'priority' => 'high',
        ]);

        $this->assertEquals('emergency', $emergencyAudio->type);
        $this->assertTrue($emergencyAudio->is_processed);
    }
}
