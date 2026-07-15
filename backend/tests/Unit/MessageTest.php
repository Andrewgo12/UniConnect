<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Models\User;
use App\Models\Conversation;
use App\Http\Resources\MessageResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $conversation;
    protected $message;

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
        ]);
        
        $this->message = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Test message content',
            'type' => 'text',
            'status' => 'sent',
        ]);
    }

    /**
     * Test message creation.
     */
    public function test_message_can_be_created(): void
    {
        $messageData = [
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'New test message',
            'type' => 'text',
            'status' => 'sent',
        ];

        $message = Message::create($messageData);

        $this->assertDatabaseHas('messages', $messageData);
        $this->assertEquals('New test message', $message->content);
        $this->assertEquals('text', $message->type);
        $this->assertEquals('sent', $message->status);
    }

    /**
     * Test message user relationship.
     */
    public function test_message_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->message->user);
        $this->assertEquals($this->user->id, $this->message->user->id);
    }

    /**
     * Test message conversation relationship.
     */
    public function test_message_belongs_to_conversation(): void
    {
        $this->assertInstanceOf(Conversation::class, $this->message->conversation);
        $this->assertEquals($this->conversation->id, $this->message->conversation->id);
    }

    /**
     * Test message accessibility data.
     */
    public function test_message_can_have_accessibility_data(): void
    {
        $accessibilityData = [
            'screen_reader_optimized' => true,
            'voice_commands' => true,
            'high_contrast' => false,
        ];

        $message = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Accessible message',
            'accessibility_data' => $accessibilityData,
        ]);

        $this->assertEquals($accessibilityData, $message->accessibility_data);
    }

    /**
     * Test message with file attachment.
     */
    public function test_message_can_have_file_attachment(): void
    {
        $metadata = [
            'file_path' => 'uploads/test_file.pdf',
            'file_name' => 'test_file.pdf',
            'file_size' => 1024,
        ];

        $message = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Message with file',
            'metadata' => $metadata,
        ]);

        $this->assertEquals($metadata, $message->metadata);
    }

    /**
     * Test message priority levels.
     */
    public function test_message_can_have_priority(): void
    {
        $highPriorityMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'High priority message',
            'priority' => 'high',
        ]);

        $lowPriorityMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Low priority message',
            'priority' => 'low',
        ]);

        $this->assertEquals('high', $highPriorityMessage->priority);
        $this->assertEquals('low', $lowPriorityMessage->priority);
    }

    /**
     * Test message status transitions.
     */
    public function test_message_status_transitions(): void
    {
        $message = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Draft message',
            'status' => 'draft',
        ]);

        // Send message
        $message->update(['status' => 'sent']);
        $this->assertEquals('sent', $message->status);

        // Read message
        $message->update(['status' => 'read']);
        $this->assertEquals('read', $message->status);

        // Delete message
        $message->update(['status' => 'deleted']);
        $this->assertEquals('deleted', $message->status);
    }

    /**
     * Test message resource transformation.
     */
    public function test_message_resource_transformation(): void
    {
        $resource = new MessageResource($this->message);
        $req = Request::create('/');

        $this->assertEquals($this->message->id, $resource['id']);
        $this->assertEquals($this->message->content, $resource['content']);
        $this->assertEquals($this->message->type, $resource['type']);
        $this->assertEquals($this->message->status, $resource['status']);
        $this->assertArrayHasKey('user', $resource->toArray($req));
        $this->assertArrayHasKey('conversation', $resource->toArray($req));
    }

    /**
     * Test message scope methods.
     */
    public function test_message_scopes(): void
    {
        // Test sent scope
        $sentMessages = Message::sent()->get();
        foreach ($sentMessages as $message) {
            $this->assertEquals('sent', $message->status);
        }

        // Test read scope
        $readMessages = Message::read()->get();
        foreach ($readMessages as $message) {
            $this->assertEquals('read', $message->status);
        }

        // Test unread scope
        $unreadMessages = Message::unread()->get();
        foreach ($unreadMessages as $message) {
            $this->assertNotEquals('read', $message->status);
        }
    }

    /**
     * Test message with voice duration.
     */
    public function test_message_can_have_voice_duration(): void
    {
        $voiceMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Voice message',
            'type' => 'voice',
            'voice_duration' => 45,
        ]);

        $this->assertEquals(45, $voiceMessage->voice_duration);
    }

    /**
     * Test message language support.
     */
    public function test_message_can_have_language(): void
    {
        $spanishMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Mensaje en español',
            'language' => 'es-CO',
        ]);

        $englishMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Message in English',
            'language' => 'en-US',
        ]);

        $this->assertEquals('es-CO', $spanishMessage->language);
        $this->assertEquals('en-US', $englishMessage->language);
    }

    /**
     * Test message with parent reply.
     */
    public function test_message_can_be_reply_to_another_message(): void
    {
        $parentMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Original message',
        ]);

        $replyMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Reply to original',
            'parent_id' => $parentMessage->id,
        ]);

        $this->assertEquals($parentMessage->id, $replyMessage->parent_id);
        $this->assertInstanceOf(Message::class, $replyMessage->parent);
    }

    /**
     * Test message pinning.
     */
    public function test_message_can_be_pinned(): void
    {
        $pinnedMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Pinned message',
            'is_pinned' => true,
        ]);

        $this->assertTrue($pinnedMessage->is_pinned);
    }

    /**
     * Test message editing.
     */
    public function test_message_can_be_edited(): void
    {
        $originalMessage = Message::factory()->create([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'content' => 'Original content',
        ]);

        $originalMessage->update([
            'content' => 'Edited content',
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        $this->assertEquals('Edited content', $originalMessage->content);
        $this->assertTrue($originalMessage->is_edited);
        $this->assertNotNull($originalMessage->edited_at);
    }
}
