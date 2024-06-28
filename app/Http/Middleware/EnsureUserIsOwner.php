<?php

namespace App\Http\Middleware;

use Closure;
use App\Domains\ConversationModule\Models\Group;
use App\Traits\ResponseService;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsOwner
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

        // Get the group ID from the request
        $groupId = $request->route('group_id');

        // Find the group
        $group = Group::find($groupId);

        if (empty($group)) {
            return ResponseService::error("Request Error: Group not found", [], 400);
        }

        // Check if the group exists and if the user is a member of the group
        if ($group->owner_id !== $user->id) {
            return ResponseService::error('Authorization Error: You cannot perform this action', [], 401);
        }
        return $next($request);
    }
}
