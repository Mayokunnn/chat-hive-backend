<?php

namespace App\Http\Controllers;

use App\Domains\UserModule\Requests\UpdateUserRequest;
use App\Domains\UserModule\Services\UserService;
use App\Traits\ResponseService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser($user_id)
    {
        return UserService::getUser($user_id);
    }

    public function updateUser(UpdateUserRequest $request, $user_id)
    {
        if (auth()->user()->id != $user_id) {
            return ResponseService::error('Authorization Error: You cannot perform this action', [], 401);
        }
        
        return UserService::updateUser($user_id, $request);
    }

    public function deleteUser($user_id){
        return UserService::deleteUser($user_id);
    }

    public function getUserConversations($user_id){
        return UserService::getUserConversations($user_id);
    }
}
