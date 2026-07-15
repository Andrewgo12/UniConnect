<?php

namespace Tests\Unit;

use App\Models\SignLanguage;
use App\Models\User;
use App\Http\Resources\SignLanguageResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class SignLanguageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $signLanguage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'accessibility_needs' => ['deaf'],
        ]);
        
        $this->signLanguage = SignLanguage::factory()->create([
            'title' => 'Test Sign Language',
            'description' => 'Test sign language description',
            'category' => 'basic',
            'difficulty_level' => 'beginner',
            'region' => 'colombian',
            'user_id' => $this->user->id,
            'created_by' => $this->user->id,
        ]);
    }

    /**
     * Test sign language creation.
     */
    public function test_sign_language_can_be_created(): void
    {
        $signLanguageData = [
            'title' => 'New Test Sign Language',
            'description' => 'New test description',
            'category' => 'medical',
            'difficulty_level' => 'intermediate',
            'region' => 'international',
            'tags' => ['emergency', 'medical'],
            'is_public' => true,
        ];

        $signLanguage = SignLanguage::create(array_merge($signLanguageData, [
            'user_id' => $this->user->id,
        ]));

        $this->assertDatabaseHas('sign_languages', [
            'title' => 'New Test Sign Language',
            'description' => 'New test description',
            'category' => 'medical',
            'difficulty_level' => 'intermediate',
            'region' => 'international',
            'tags' => json_encode(['emergency', 'medical']),
            'is_public' => 1,
        ]);
        $this->assertEquals('New Test Sign Language', $signLanguage->title);
        $this->assertEquals('medical', $signLanguage->category);
        $this->assertEquals('intermediate', $signLanguage->difficulty_level);
    }

    /**
     * Test sign language user relationship.
     */
    public function test_sign_language_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->signLanguage->user);
        $this->assertEquals($this->user->id, $this->signLanguage->user->id);
    }

    /**
     * Test sign language categories.
     */
    public function test_sign_language_categories(): void
    {
        $categories = ['basic', 'medical', 'emergency', 'education', 'custom'];

        foreach ($categories as $category) {
            $signLanguage = SignLanguage::factory()->create([
                'title' => "Test {$category} sign",
                'category' => $category,
            ]);

            $this->assertEquals($category, $signLanguage->category);
        }
    }

    /**
     * Test sign language difficulty levels.
     */
    public function test_sign_language_difficulty_levels(): void
    {
        $levels = ['beginner', 'intermediate', 'advanced'];

        foreach ($levels as $level) {
            $signLanguage = SignLanguage::factory()->create([
                'title' => "Test {$level} sign",
                'difficulty_level' => $level,
            ]);

            $this->assertEquals($level, $signLanguage->difficulty_level);
        }
    }

    /**
     * Test sign language regions.
     */
    public function test_sign_language_regions(): void
    {
        $regions = ['colombian', 'international', 'local'];

        foreach ($regions as $region) {
            $signLanguage = SignLanguage::factory()->create([
                'title' => "Test {$region} sign",
                'region' => $region,
            ]);

            $this->assertEquals($region, $signLanguage->region);
        }
    }

    /**
     * Test sign language tags.
     */
    public function test_sign_language_can_have_tags(): void
    {
        $tags = ['emergency', 'medical', 'education', 'daily', 'custom'];

        $signLanguage = SignLanguage::factory()->create([
            'title' => 'Sign with tags',
            'tags' => $tags,
        ]);

        $this->assertEquals($tags, $signLanguage->tags);
        $this->assertIsArray($signLanguage->tags);
    }

    /**
     * Test sign language approval status.
     */
    public function test_sign_language_approval_status(): void
    {
        $approvedSign = SignLanguage::factory()->create([
            'title' => 'Approved Sign',
            'is_approved' => true,
        ]);

        $pendingSign = SignLanguage::factory()->create([
            'title' => 'Pending Sign',
            'is_approved' => false,
        ]);

        $this->assertTrue($approvedSign->is_approved);
        $this->assertFalse($pendingSign->is_approved);
    }

    /**
     * Test sign language public status.
     */
    public function test_sign_language_public_status(): void
    {
        $publicSign = SignLanguage::factory()->create([
            'title' => 'Public Sign',
            'is_public' => true,
        ]);

        $privateSign = SignLanguage::factory()->create([
            'title' => 'Private Sign',
            'is_public' => false,
        ]);

        $this->assertTrue($publicSign->is_public);
        $this->assertFalse($privateSign->is_public);
    }

    /**
     * Test sign language usage count.
     */
    public function test_sign_language_usage_count(): void
    {
        $signLanguage = SignLanguage::factory()->create([
            'title' => 'Popular Sign',
            'usage_count' => 150,
        ]);

        $this->assertEquals(150, $signLanguage->usage_count);
    }

    /**
     * Test sign language duration.
     */
    public function test_sign_language_can_have_duration(): void
    {
        $signLanguage = SignLanguage::factory()->create([
            'title' => 'Sign with duration',
            'duration' => 120, // 2 minutes
        ]);

        $this->assertEquals(120, $signLanguage->duration);
    }

    /**
     * Test sign language transcript.
     */
    public function test_sign_language_can_have_transcript(): void
    {
        $transcript = 'This is the transcript of the sign language video';

        $signLanguage = SignLanguage::factory()->create([
            'title' => 'Sign with transcript',
            'transcript' => $transcript,
        ]);

        $this->assertEquals($transcript, $signLanguage->transcript);
    }

    /**
     * Test sign language language support.
     */
    public function test_sign_language_can_have_language(): void
    {
        $spanishSign = SignLanguage::factory()->create([
            'title' => 'Spanish Sign',
            'language' => 'es-CO',
        ]);

        $englishSign = SignLanguage::factory()->create([
            'title' => 'English Sign',
            'language' => 'en-US',
        ]);

        $this->assertEquals('es-CO', $spanishSign->language);
        $this->assertEquals('en-US', $englishSign->language);
    }

    /**
     * Test sign language resource transformation.
     */
    public function test_sign_language_resource_transformation(): void
    {
        $resource = new SignLanguageResource($this->signLanguage->load('user'));
        $req = Request::create('/');

        $this->assertEquals($this->signLanguage->id, $resource['id']);
        $this->assertEquals($this->signLanguage->title, $resource['title']);
        $this->assertEquals($this->signLanguage->category, $resource['category']);
        $this->assertEquals($this->signLanguage->difficulty_level, $resource['difficulty_level']);
        $this->assertEquals($this->signLanguage->region, $resource['region']);
        $this->assertArrayHasKey('user', $resource->toArray($req));
    }

    /**
     * Test sign language scope methods.
     */
    public function test_sign_language_scopes(): void
    {
        // Test byCategory scope
        $medicalSigns = SignLanguage::byCategory('medical')->get();
        foreach ($medicalSigns as $sign) {
            $this->assertEquals('medical', $sign->category);
        }

        // Test byDifficulty scope
        $beginnerSigns = SignLanguage::byDifficulty('beginner')->get();
        foreach ($beginnerSigns as $sign) {
            $this->assertEquals('beginner', $sign->difficulty_level);
        }

        // Test byRegion scope
        $colombianSigns = SignLanguage::byRegion('colombian')->get();
        foreach ($colombianSigns as $sign) {
            $this->assertEquals('colombian', $sign->region);
        }

        // Test public scope
        $publicSigns = SignLanguage::public()->get();
        foreach ($publicSigns as $sign) {
            $this->assertTrue($sign->is_public);
        }

        // Test approved scope
        $approvedSigns = SignLanguage::approved()->get();
        foreach ($approvedSigns as $sign) {
            $this->assertTrue($sign->is_approved);
        }
    }

    /**
     * Test sign language metadata.
     */
    public function test_sign_language_can_have_metadata(): void
    {
        $metadata = [
            'accessibility_features' => ['slow_motion', 'captions'],
            'technical_specs' => [
                'resolution' => '1080p',
                'frame_rate' => '30fps',
            ],
            'usage_statistics' => [
                'views_today' => 25,
                'favorites_count' => 10,
            ],
        ];

        $signLanguage = SignLanguage::factory()->create([
            'title' => 'Sign with metadata',
            'metadata' => $metadata,
        ]);

        $this->assertEquals($metadata, $signLanguage->metadata);
    }
}
