<?php

namespace Tests\Unit;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Message;
use App\Http\Resources\ConversationResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $conversation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'accessibility_needs' => ['blind', 'deaf'],
        ]);
        
        $this->conversation = Conversation::factory()->create([
            'title' => 'Test Conversation',
            'created_by' => $this->user->id,
            'type' => 'direct',
            'status' => 'active',
        ]);
    }

    /**
     * Test conversation creation.
     */
    public function test_conversation_can_be_created(): void
    {
        $conversationData = [
            'title' => 'New Test Conversation',
            'description' => 'Test conversation description',
            'type' => 'group',
            'status' => 'active',
            'created_by' => $this->user->id,
        ];

        $conversation = Conversation::create($conversationData);

        $this->assertDatabaseHas('conversations', $conversationData);
        $this->assertEquals('New Test Conversation', $conversation->title);
        $this->assertEquals('group', $conversation->type);
        $this->assertEquals('active', $conversation->status);
    }

    /**
     * Test conversation creator relationship.
     */
    public function test_conversation_belongs_to_creator(): void
    {
        $this->assertInstanceOf(User::class, $this->conversation->creator);
        $this->assertEquals($this->user->id, $this->conversation->creator->id);
    }

    /**
     * Test conversation participants relationship.
     */
    public function test_conversation_can_have_participants(): void
    {
        $participant1 = User::factory()->create();
        $participant2 = User::factory()->create();

        $this->conversation->participants()->attach([
            $participant1->id => ['role' => 'admin'],
            $participant2->id => ['role' => 'member'],
        ]);

        $this->assertCount(2, $this->conversation->participants);
        $this->assertDatabaseHas('conversation_participants', [
            'user_id' => $participant1->id,
            'role' => 'admin',
        ]);
    }

    /**
     * Test conversation messages relationship.
     */
    public function test_conversation_can_have_messages(): void
    {
        $messages = Message::factory()->count(3)->create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertCount(3, $this->conversation->messages);
        foreach ($messages as $message) {
            $this->assertEquals($this->conversation->id, $message->conversation_id);
        }
    }

    /**
     * Test conversation accessibility features.
     */
    public function test_conversation_can_have_accessibility_settings(): void
    {
        $settings = [
            'voice_commands_enabled' => true,
            'high_contrast_mode' => true,
            'large_text_mode' => false,
            'screen_reader_optimized' => true,
        ];

        $conversation = Conversation::factory()->create([
            'title' => 'Accessible Conversation',
            'settings' => $settings,
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals($settings, $conversation->settings);
    }

    /**
     * Test conversation status transitions.
     */
    public function test_conversation_status_transitions(): void
    {
        $conversation = Conversation::factory()->create([
            'title' => 'Test Conversation',
            'status' => 'active',
        ]);

        // Close conversation
        $conversation->update([
            'status' => 'closed',
            'closed_by' => $this->user->id,
            'closed_at' => now(),
        ]);

        $this->assertEquals('closed', $conversation->status);
        $this->assertEquals($this->user->id, $conversation->closed_by);
        $this->assertNotNull($conversation->closed_at);

        // Archive conversation
        $conversation->update(['status' => 'archived']);
        $this->assertEquals('archived', $conversation->status);
    }

    /**
     * Test conversation priority levels.
     */
    public function test_conversation_can_have_priority(): void
    {
        $highPriorityConversation = Conversation::factory()->create([
            'title' => 'High Priority Conversation',
            'priority' => 'high',
        ]);

        $lowPriorityConversation = Conversation::factory()->create([
            'title' => 'Low Priority Conversation',
            'priority' => 'low',
        ]);

        $this->assertEquals('high', $highPriorityConversation->priority);
        $this->assertEquals('low', $lowPriorityConversation->priority);
    }

    /**
     * Test conversation categories.
     */
    public function test_conversation_can_have_category(): void
    {
        $categories = ['general', 'medical', 'emergency', 'education', 'accessibility'];

        foreach ($categories as $category) {
            $conversation = Conversation::factory()->create([
                'title' => "Test {$category} conversation",
                'category' => $category,
            ]);

            $this->assertEquals($category, $conversation->category);
        }
    }

    /**
     * Test conversation muting.
     */
    public function test_conversation_can_be_muted(): void
    {
        $conversation = Conversation::factory()->create([
            'title' => 'Muted Conversation',
            'is_muted' => true,
        ]);

        $this->assertTrue($conversation->is_muted);
    }

    /**
     * Test conversation pinning.
     */
    public function test_conversation_can_be_pinned(): void
    {
        $conversation = Conversation::factory()->create([
            'title' => 'Pinned Conversation',
            'is_pinned' => true,
        ]);

        $this->assertTrue($conversation->is_pinned);
    }

    /**
     * Test conversation resource transformation.
     */
    public function test_conversation_resource_transformation(): void
    {
        $resource = new ConversationResource($this->conversation);
        $req = Request::create('/');

        $this->assertEquals($this->conversation->id, $resource['id']);
        $this->assertEquals($this->conversation->title, $resource['title']);
        $this->assertEquals($this->conversation->type, $resource['type']);
        $this->assertEquals($this->conversation->status, $resource['status']);
        $this->assertArrayHasKey('creator', $resource->toArray($req));
        $this->assertArrayHasKey('participants', $resource->toArray($req));
    }

    /**
     * Test conversation scope methods.
     */
    public function test_conversation_scopes(): void
    {
        // Test active scope
        $activeConversations = Conversation::active()->get();
        foreach ($activeConversations as $conversation) {
            $this->assertEquals('active', $conversation->status);
        }

        // Test closed scope
        $closedConversations = Conversation::closed()->get();
        foreach ($closedConversations as $conversation) {
            $this->assertEquals('closed', $conversation->status);
        }

        // Test byUser scope
        $userConversations = Conversation::byUser($this->user->id)->get();
        foreach ($userConversations as $conversation) {
            $this->assertEquals($this->user->id, $conversation->created_by);
        }
    }

    /**
     * Test conversation with last message.
     */
    public function test_conversation_can_have_last_message(): void
    {
        $lastMessage = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user->id,
            'content' => 'Last message content',
        ]);

        $this->conversation->update(['last_message_id' => $lastMessage->id]);

        $this->assertEquals($lastMessage->id, $this->conversation->last_message_id);
        $this->assertInstanceOf(Message::class, $this->conversation->lastMessage);
    }

    /**
     * Test conversation metadata.
     */
    public function test_conversation_can_have_metadata(): void
    {
        $metadata = [
            'accessibility_features' => ['voice_commands', 'screen_reader'],
            'custom_settings' => [
                'theme' => 'dark',
                'font_size' => 'large',
            ],
            'integration_data' => [
                'external_system_id' => 'ext_123',
                'sync_enabled' => true,
            ],
        ];

        $conversation = Conversation::factory()->create([
            'title' => 'Conversation with Metadata',
            'metadata' => $metadata,
        ]);

        $this->assertEquals($metadata, $conversation->metadata);
    }
}
