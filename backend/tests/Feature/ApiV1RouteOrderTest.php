<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1RouteOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_static_paths_resolve_before_dynamic_route_parameters(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/sign-languages/categories')->assertOk();

        $this->getJson('/api/v1/images/profile')->assertOk();

        $this->getJson('/api/v1/accessibility/settings')->assertOk();

        $this->postJson('/api/v1/audio/speech-to-text', [])->assertStatus(422);

        $this->postJson('/api/v1/audio/text-to-speech', [])->assertStatus(422);

        $this->getJson('/api/v1/messages')->assertOk();
    }
}
