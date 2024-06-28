<?php

namespace App\Domains\UserModule\Services;

use App\Domains\ConversationModule\Models\Conversation;
use App\Domains\ConversationModule\Resources\ConversationResource;
use App\Domains\UserModule\Repositories\UserRepository;
use App\Domains\UserModule\Resources\UserResource;
use App\Traits\ResponseService;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public static function  getUser($id)
    {
        try {
            $user = UserRepository::getUserById($id);

            if (!$user || empty($user)) {
                return ResponseService::error("Request Error: User does not exist", [], 400);
            }

            return ResponseService::success('Success', new UserResource($user), 200);
        } catch (Exception $e) {
            return ResponseService::error("Server Error: User could not be found", [], 500);
        }
    }

    public static function  updateUser($id, $request)
    {
        try {
            $user = UserRepository::getUserById($id);

            if (!$user || empty($user)) {
                return ResponseService::error("Request Error: User does not exist", [], 400);
            }

            $user = UserRepository::updateUser($id, $request);

            return ResponseService::success('Success: User updated', new UserResource($user), 200);
        } catch (Exception $e) {
            return ResponseService::error("Server Error: User could not be found", [], 500);
        }
    }

    public static function deleteUser($id)
    {
        try {
            $user = Auth::user();
            UserRepository::updateUserLoginStatus($user->email, 0);

            Auth::logout();

            UserRepository::deleteUser($id);

            return ResponseService::success("Account deleted", [], 200);
        } catch (Exception $e) {
            return ResponseService::error("Server Error: User could not be deleted", [], 500);
        }
    }

    public static function getUserConversations($userId)
    {
        try {
            $user = UserRepository::getUserById($userId);

            if (!$user || empty($user)) {
                return ResponseService::error("Request Error: User does not exist", [], 400);
            }

            $conversations = UserRepository::getAllConversationsOfAUser($userId);

            if (!$conversations || empty($conversations)) {
                return ResponseService::success('No conversations yet!', [], 200);
            }

            return ResponseService::success('Success', ConversationResource::collection($conversations), 200);
        } catch (Exception $e) {
            return ResponseService::error("Server Error: User could not be found", [], 500);
        }
    }
}
