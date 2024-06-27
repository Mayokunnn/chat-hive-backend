<?php

namespace App\Http\Controllers;

use App\Domains\ConversationModule\Requests\AddConversationUserRequest;
use App\Domains\ConversationModule\Services\ConversationService;
use Illuminate\Http\Request;

class ConversationUserController extends Controller
{
    public function getAll($conversation_id)
    {
        return ConversationService::getUsersInAConversation($conversation_id);
    }

    // public function add(AddConversationUserRequest $request, $conversation_id){
    //     return ConversationService::addUserToConversation($request, $conversation_id);
    // }
}
