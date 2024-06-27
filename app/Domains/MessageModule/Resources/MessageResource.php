<?php

namespace App\Domains\MessageModule\Resources;

use App\Domains\ConversationModule\Resources\ConversationResource;
use App\Domains\UserModule\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'message',
            'id' => $this->id,
            'attributes' => [
                'type' => $this->type,
                'url' => $this->url,
                'content' => $this->content,
                'senderId' => $this->sender_id,
                'conversationId' => $this->conversation_id,
                $this->mergeWhen(
                    $request->routeIs(['messages.*']),
                    [
                        'emailVerifiedAt' => $this->email_verified_at,
                        'createdAt' => $this->created_at,
                        'updatedAt' => $this->updated_at,
                    ]
                )
            ],
            'includes' => [
                'data' => [
                    'conversation' => new ConversationResource($this->conversation),
                    'sender' => new UserResource($this->sender)
                ]
            ]
        ];
    }
}
