<?php

namespace App\Domains\ConversationModule\Resources;

use App\Domains\UserModule\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'group',
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->conversation->image,
            'ownerId' => $this->owner_id,
            'conversationId' => $this->conversation_id,
            $this->mergeWhen(
                $request->routeIs(['update-group-conversation', 'create-group-conversation', 'get-group-conversation']),
                [
                    'createdAt' => $this->created_at,
                    'updatedAt' => $this->updated_at,
                    'includes' => [
                        'members' => UserResource::collection($this->members),
                        'conversation' => new ConversationResource($this->conversation)

                    ]
                ]
            ),

        ];
    }
}
