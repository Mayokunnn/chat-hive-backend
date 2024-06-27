<?php


namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Conversation;
use App\Domains\MessageModule\Models\Message;

class MessageTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test sending a message.
     *
     * @return void
     */
    public function testSendMessage()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->actingAs($user, 'api')->post('api/v1/messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'type' => 'text',
            'content' => 'Hello, world!'
        ]);

        $this->seeStatusCode(201);
        $this->seeJsonStructure([
            'message' => [
                'id',
                'conversation_id',
                'sender_id',
                'type',
                'content',
                'created_at',
                'updated_at'
            ]
        ]);

        $this->seeInDatabase('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'type' => 'text',
            'content' => 'Hello, world!'
        ]);
    }

    /**
     * Test getting messages.
     *
     * @return void
     */
    public function testGetMessages()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'type' => 'text',
            'content' => 'Hello, world!'
        ]);

        $this->actingAs($user, 'api')->get('/messages', [
            'conversation_id' => $conversation->id
        ]);

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'messages' => [
                '*' => [
                    'id',
                    'conversation_id',
                    'sender_id',
                    'type',
                    'content',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);

        $this->seeJsonContains([
            'id' => $message->id,
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'type' => 'text',
            'content' => 'Hello, world!'
        ]);
    }
}

