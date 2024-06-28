<?php

namespace App\Domains\ConversationModule\Resources;

use App\Domains\UserModule\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'conversation',
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            $this->mergeWhen(
                $request->routeIs(['get-user-conversations','get-conversation', 'create-conversation', 'update-conversation']),
                [
                    'emailVerifiedAt' => $this->email_verified_at,
                    'createdAt' => $this->created_at,
                    'updatedAt' => $this->updated_at,
                    'includes' => [
                        'users' => UserResource::collection($this->users)
                    ]
                ]
            ),

            // 'links' => [
            //     'self' => route('conversations', ['conversation' => $this->id])
            // ]
        ];
    }
}
