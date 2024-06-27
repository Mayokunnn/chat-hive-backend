<?php

namespace App\Http\Controllers;

use App\Domains\ConversationModule\Requests\CreateConversationRequest;
use App\Domains\ConversationModule\Requests\UpdateConversationRequest;
use App\Domains\ConversationModule\Services\ConversationService;
use App\Traits\ResponseService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function getAll()
    {
        return ConversationService::getAll();
    }

    public function getSingleConversation($conversation_id)
    {
        return ConversationService::getConversation($conversation_id);
    }

    public function create(CreateConversationRequest $request)
    {
        return ConversationService::create($request);
    }

    public function update(UpdateConversationRequest $request, $conversation_id){
        return ConversationService::update($request, $conversation_id);
    }

    public function delete($conversation_id){
        return ConversationService::delete($conversation_id);   
    }

}
