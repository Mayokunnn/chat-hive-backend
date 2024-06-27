<?php

namespace App\Domains\ConversationModule\Services;

use App\Domains\ConversationModule\Models\Conversation;
use App\Domains\ConversationModule\Repositories\ConversationRepository;
use App\Domains\ConversationModule\Resources\ConversationResource;
use App\Domains\UserModule\Models\User;
use App\Domains\UserModule\Resources\UserResource;
use App\Traits\ResponseService;

class ConversationService
{
    public static function getAll()
    {
        $conversations =  ConversationRepository::getAllConversations();

        if (count($conversations) == 0) {
            return ResponseService::error("Request Error: No existing conversations", [], 400);
        }

        return ResponseService::success('', [ConversationResource::collection($conversations)], 200);
    }

    public static function getConversation($id)
    {
        $conversation = ConversationRepository::getConversationById($id);

        if (empty($conversation)) {
            return ResponseService::error('Request Error: Conversation not found', [], 400);
        }

        return ResponseService::success('Success', [new ConversationResource($conversation)], 200);
    }

    public static function create($request)
    {
        $userIds = $request->input('user_ids');

        // Sort user IDs to ensure consistent order for comparison
        sort($userIds);

        // Check if a conversation with the same set of user IDs already exists
        $existingConversation = Conversation::whereHas('users', function ($query) use ($userIds) {
            $query->whereIn('user_id', $userIds)
                ->groupBy('conversation_id')
                ->havingRaw('COUNT(DISTINCT user_id) = ?', [count($userIds)]);
        })->first();

        if ($existingConversation) {
            return ResponseService::success('Conversation already exists!', [new ConversationResource($existingConversation), 'existing' => true], 200);
        }

        $user = auth()->user();
        if (!in_array($user->id, $userIds)) {
            return ResponseService::error('Authorization Error: You are not authorized to create this conversation', [], 403);
        }
        $name = $request->input('name');
        $image = $request->file('image');
        $conversation =  ConversationRepository::createPersonalConversation($userIds, $name, $image);

        if (empty($conversation)) {
            return ResponseService::error('Request Error: Conversation could not be created', [], 400);
        }

        if (count($userIds) == 2) {
            $user1 = User::find($userIds[0]);
            $user2 = User::find($userIds[1]);

            // Update conversation name for each user
            $conversation->users()->updateExistingPivot($user1->id, ['name' => $user2->name]);
            $conversation->users()->updateExistingPivot($user2->id, ['name' => $user1->name]);
        }


        return ResponseService::success('Conversation created!', [new ConversationResource($conversation)], 200);
    }

    public static function update($request, $id)
    {
        $conversation = ConversationRepository::getConversationById($id);

        if (empty($conversation)) {
            return ResponseService::error('Request Error: Conversation not found', [], 400);
        }

        $conversation = ConversationRepository::updatePersonalConversation($conversation->id, $request);

        if (empty($conversation)) {
            return ResponseService::error('Conversation could not be updated. Try again later', [], 500);
        }

        return ResponseService::success('Conversation Updated', [new ConversationResource($conversation)], 200);
    }

    public static function delete($id)
    {
        $conversation = ConversationRepository::getConversationById($id);

        if (empty($conversation)) {
            return ResponseService::error('Request Error: Conversation not found', [], 400);
        }

        $deleted = ConversationRepository::deletePersonalConversation($conversation->id);

        if (!$deleted) {
            return ResponseService::error('Conversation could not be deleted. Try again later', [], 500);
        }

        return ResponseService::success('Conversation Deleted');
    }

    public static function getUsersInAConversation($conversation_id){
        $conversation = ConversationRepository::getConversationById($conversation_id);

        if (empty($conversation)) {
            return ResponseService::error('Request Error: Conversation not found', [], 400);
        }

        $users = ConversationRepository::getAllUsersInConversation($conversation_id);

        return ResponseService::success('Success', [UserResource::collection($users)],200);
    }

    // public static function addUserToConversation($request, $conversation_id){
    //     $conversation = ConversationRepository::getConversationById($conversation_id);

    //     if (empty($conversation)) {
    //         return ResponseService::error('Request Error: Conversation not found', [], 400);
    //     }

    //     $user = User::find($request->input('userId'));

    //     if (empty($user)) {
    //         return ResponseService::error('Request Error: User not found', [], 400);
    //     }

    //     $conversation = ConversationRepository::addUserToConversation($conversation->id, $request->input('userId'));
    // }
}
