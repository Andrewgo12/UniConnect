<?php

namespace Tests\Unit;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'accessibility_needs' => ['blind', 'deaf'],
            'is_active' => true,
        ]);

        Profile::create([
            'user_id' => $this->user->id,
            'name' => 'Test User',
            'blind' => true,
            'deaf' => true,
            'mute' => false,
            'preferences' => ['bio' => 'Demo bio'],
        ]);
    }

    protected function tearDown(): void
    {
        Queue::fake();
        Notification::fake();
    parent::tearDown();
    }

    /**
     * Test user login with valid credentials.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $request = new LoginRequest([
            'email' => 'test@example.com',
            'password' => 'password123',
            'device_type' => 'mobile',
            'accessibility_mode' => 'screen_reader',
        ]);

        $this->assertTrue($request->authorize());

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_at',
            'user',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'accessibility_needs' => json_encode(['blind', 'deaf']),
        ]);
    }

    /**
     * Test user login with invalid credentials.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $request = new LoginRequest([
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test user registration with valid data.
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $userData = [
            'name' => 'New Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'accessibility_needs' => ['mute'],
            'terms_accepted' => true,
            'privacy_accepted' => true,
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'name', 'email']);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'accessibility_needs' => json_encode(['mute']),
        ]);
    }

    /**
     * Test user registration with invalid data.
     */
    public function test_user_cannot_register_with_invalid_data(): void
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test password reset with valid token.
     */
    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /**
     * Test password reset with invalid token.
     */
    public function test_user_cannot_reset_password_with_invalid_token(): void
    {
        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'test@example.com',
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['token']);
    }

    /**
     * Test user logout.
     */
    public function test_user_can_logout(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully logged out']);
    }

    /**
     * Test user profile update.
     */
    public function test_user_can_update_profile(): void
    {
        $updateData = [
            'name' => 'Updated Test User',
            'phone' => '+1234567890',
            'accessibility_needs' => ['blind'],
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/v1/user/profile', $updateData);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Profile updated successfully']);
    }

    /**
     * Test user resource transformation.
     */
    public function test_user_resource_transformation(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/user/profile');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'accessibility_needs',
            'profile' => [
                'bio',
                'preferences',
                'accessibility_settings',
            ],
        ]);
    }
}
