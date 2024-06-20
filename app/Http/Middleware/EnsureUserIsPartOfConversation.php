<?php

namespace App\Http\Middleware;

use Closure;
use App\Domains\ConversationModule\Models\Conversation;
use App\Traits\ResponseService;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsPartOfConversation
{

    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */


    public function handle($request, Closure $next)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get the conversation ID from the request
        $conversation_id = $request->conversation_id;

        // Find the conversation
        $conversation = Conversation::find($conversation_id);

        if(empty($conversation)){
            return ResponseService::error("Request Error: Conversation not found", [], 400);
        }

        // Check if the user is part of the conversation
        if (!$conversation || !$conversation->users->contains($user->id)) {
            return ResponseService::error('Authorization Error: You cannot perfrom this action', [$conversation], 403);
        }

        return $next($request);
    }
}
