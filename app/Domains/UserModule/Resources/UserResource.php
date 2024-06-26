<?php

namespace App\Domains\UserModule\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'user',
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'image' => $this->image,
            'username' => $this->username,
            'loggedIn' => $this->loggedIn,
            $this->mergeWhen(
                $request->routeIs(['users.*']),
                [
                    'emailVerifiedAt' => $this->email_verified_at,
                    'createdAt' => $this->created_at,
                    'updatedAt' => $this->updated_at,
                ]
            ),
            // 'links' => [
            //     'self' => route('users.show', ['user' => $this->id])
            // ]
        ];
    }
}
