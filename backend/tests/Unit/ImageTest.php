<?php

namespace Tests\Unit;

use App\Models\Image;
use App\Models\User;
use App\Http\Resources\ImageResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $image;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'accessibility_needs' => ['blind'],
        ]);
        
        $this->image = Image::factory()->create([
            'title' => 'Test Image',
            'user_id' => $this->user->id,
            'description' => 'Test image description',
            'type' => 'profile',
            'file_path' => 'uploads/test_image.jpg',
            'original_name' => 'test_image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024000,
            'width' => 1920,
            'height' => 1080,
            'alt_text' => 'Test image alt text',
            'created_by' => $this->user->id,
        ]);
    }

    /**
     * Test image creation.
     */
    public function test_image_can_be_created(): void
    {
        $imageData = [
            'title' => 'New Test Image',
            'description' => 'New test description',
            'type' => 'sign_language',
            'file_path' => 'uploads/new_image.jpg',
            'original_name' => 'new_image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 2048000,
            'width' => 1280,
            'height' => 720,
            'alt_text' => 'New image alt text',
            'tags' => ['emergency', 'medical'],
            'created_by' => $this->user->id,
            'user_id' => $this->user->id,
        ];

        $image = Image::create($imageData);

        $this->assertDatabaseHas('images', [
            'title' => 'New Test Image',
            'description' => 'New test description',
            'type' => 'sign_language',
            'tags' => json_encode(['emergency', 'medical']),
            'created_by' => $this->user->id,
            'user_id' => $this->user->id,
        ]);
        $this->assertEquals('New Test Image', $image->title);
        $this->assertEquals('sign_language', $image->type);
        $this->assertEquals(1280, $image->width);
        $this->assertEquals(720, $image->height);
    }

    /**
     * Test image user relationship.
     */
    public function test_image_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->image->user);
        $this->assertEquals($this->user->id, $this->image->user->id);
    }

    /**
     * Test image types.
     */
    public function test_image_types(): void
    {
        $types = ['profile', 'sign_language', 'emergency', 'medical', 'general'];

        foreach ($types as $type) {
            $image = Image::factory()->create([
                'title' => "Test {$type} image",
                'type' => $type,
            ]);

            $this->assertEquals($type, $image->type);
        }
    }

    /**
     * Test image dimensions.
     */
    public function test_image_dimensions(): void
    {
        $landscapeImage = Image::factory()->create([
            'title' => 'Landscape Image',
            'width' => 1920,
            'height' => 1080,
        ]);

        $portraitImage = Image::factory()->create([
            'title' => 'Portrait Image',
            'width' => 1080,
            'height' => 1920,
        ]);

        $this->assertEquals(1920, $landscapeImage->width);
        $this->assertEquals(1080, $landscapeImage->height);
        $this->assertEquals(1080, $portraitImage->width);
        $this->assertEquals(1920, $portraitImage->height);
    }

    /**
     * Test image alt text.
     */
    public function test_image_can_have_alt_text(): void
    {
        $altText = 'This is a descriptive alt text for accessibility';

        $image = Image::factory()->create([
            'title' => 'Image with alt text',
            'alt_text' => $altText,
        ]);

        $this->assertEquals($altText, $image->alt_text);
    }

    /**
     * Test image tags.
     */
    public function test_image_can_have_tags(): void
    {
        $tags = ['emergency', 'medical', 'education', 'accessibility', 'custom'];

        $image = Image::factory()->create([
            'title' => 'Image with tags',
            'tags' => $tags,
        ]);

        $this->assertEquals($tags, $image->tags);
        $this->assertIsArray($image->tags);
    }

    /**
     * Test image approval status.
     */
    public function test_image_approval_status(): void
    {
        $approvedImage = Image::factory()->create([
            'title' => 'Approved Image',
            'is_approved' => true,
        ]);

        $pendingImage = Image::factory()->create([
            'title' => 'Pending Image',
            'is_approved' => false,
        ]);

        $this->assertTrue($approvedImage->is_approved);
        $this->assertFalse($pendingImage->is_approved);
    }

    /**
     * Test image public status.
     */
    public function test_image_public_status(): void
    {
        $publicImage = Image::factory()->create([
            'title' => 'Public Image',
            'is_public' => true,
        ]);

        $privateImage = Image::factory()->create([
            'title' => 'Private Image',
            'is_public' => false,
        ]);

        $this->assertTrue($publicImage->is_public);
        $this->assertFalse($privateImage->is_public);
    }

    /**
     * Test image usage count.
     */
    public function test_image_usage_count(): void
    {
        $image = Image::factory()->create([
            'title' => 'Popular Image',
            'usage_count' => 250,
        ]);

        $this->assertEquals(250, $image->usage_count);
    }

    /**
     * Test image file properties.
     */
    public function test_image_file_properties(): void
    {
        $image = Image::factory()->create([
            'title' => 'Test File Image',
            'file_path' => 'uploads/test.jpg',
            'original_name' => 'test_image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024000,
        ]);

        $this->assertEquals('uploads/test.jpg', $image->file_path);
        $this->assertEquals('test_image.jpg', $image->original_name);
        $this->assertEquals('image/jpeg', $image->mime_type);
        $this->assertEquals(1024000, $image->size);
    }

    /**
     * Test image resource transformation.
     */
    public function test_image_resource_transformation(): void
    {
        $resource = new ImageResource($this->image);
        $req = Request::create('/');

        $this->assertEquals($this->image->id, $resource['id']);
        $this->assertEquals($this->image->title, $resource['title']);
        $this->assertEquals($this->image->type, $resource['type']);
        $this->assertEquals($this->image->width, $resource['width']);
        $this->assertEquals($this->image->height, $resource['height']);
        $this->assertEquals($this->image->alt_text, $resource['alt_text']);
        $this->assertArrayHasKey('user', $resource->toArray($req));
    }

    /**
     * Test image aspect ratio calculation.
     */
    public function test_image_aspect_ratio(): void
    {
        $landscapeImage = Image::factory()->create([
            'title' => 'Landscape Image',
            'width' => 1920,
            'height' => 1080,
        ]);

        $portraitImage = Image::factory()->create([
            'title' => 'Portrait Image',
            'width' => 1080,
            'height' => 1920,
        ]);

        $landscapeResource = new ImageResource($landscapeImage);
        $portraitResource = new ImageResource($portraitImage);
        $req = Request::create('/');

        $this->assertEquals(1.78, round($landscapeResource->toArray($req)['aspect_ratio'], 2));
        $this->assertEquals(0.56, round($portraitResource->toArray($req)['aspect_ratio'], 2));
    }

    /**
     * Test image size formatting.
     */
    public function test_image_size_formatting(): void
    {
        $image = Image::factory()->create([
            'title' => 'Test Image',
            'size' => 2097152, // 2MB
        ]);

        $resource = new ImageResource($image);
        $req = Request::create('/');
        $this->assertEquals('2.00 MB', $resource->toArray($req)['formatted_size']);
    }

    /**
     * Test image scope methods.
     */
    public function test_image_scopes(): void
    {
        // Test byType scope
        $profileImages = Image::byType('profile')->get();
        foreach ($profileImages as $image) {
            $this->assertEquals('profile', $image->type);
        }

        // Test approved scope
        $approvedImages = Image::approved()->get();
        foreach ($approvedImages as $image) {
            $this->assertTrue($image->is_approved);
        }

        // Test public scope
        $publicImages = Image::public()->get();
        foreach ($publicImages as $image) {
            $this->assertTrue($image->is_public);
        }

        // Test byUser scope
        $userImages = Image::byUser($this->user->id)->get();
        foreach ($userImages as $image) {
            $this->assertEquals($this->user->id, $image->created_by);
        }
    }

    /**
     * Test image metadata.
     */
    public function test_image_can_have_metadata(): void
    {
        $metadata = [
            'accessibility_features' => ['alt_text', 'high_contrast'],
            'technical_specs' => [
                'color_profile' => 'sRGB',
                'compression' => 'JPEG',
            ],
            'usage_statistics' => [
                'views_today' => 50,
                'downloads_count' => 25,
            ],
        ];

        $image = Image::factory()->create([
            'title' => 'Image with metadata',
            'metadata' => $metadata,
        ]);

        $this->assertEquals($metadata, $image->metadata);
    }

    /**
     * Test image emergency processing.
     */
    public function test_image_emergency_processing(): void
    {
        $emergencyImage = Image::factory()->create([
            'title' => 'Emergency Image',
            'type' => 'emergency',
            'priority' => 'high',
        ]);

        $this->assertEquals('emergency', $emergencyImage->type);
        $this->assertEquals('high', $emergencyImage->priority);
    }

    /**
     * Test image medical processing.
     */
    public function test_image_medical_processing(): void
    {
        $medicalImage = Image::factory()->create([
            'title' => 'Medical Image',
            'type' => 'medical',
            'description' => 'Medical record image',
        ]);

        $this->assertEquals('medical', $medicalImage->type);
        $this->assertStringContainsStringIgnoringCase('medical', $medicalImage->description);
    }

    /**
     * Test image accessibility optimization.
     */
    public function test_image_accessibility_optimization(): void
    {
        $accessibleImage = Image::factory()->create([
            'title' => 'Accessible Image',
            'type' => 'sign_language',
            'alt_text' => 'Detailed description for screen readers',
            'tags' => ['accessible', 'screen_reader'],
        ]);

        $this->assertEquals('sign_language', $accessibleImage->type);
        $this->assertStringContainsString('screen readers', $accessibleImage->alt_text);
        $this->assertContains('accessible', $accessibleImage->tags);
    }
}
