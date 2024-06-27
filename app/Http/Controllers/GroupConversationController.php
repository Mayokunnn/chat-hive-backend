<?php

namespace App\Http\Controllers;

use App\Domains\ConversationModule\Requests\AddGroupParticipationRequest;
use App\Domains\ConversationModule\Requests\CreateGroupRequest;
use App\Domains\ConversationModule\Requests\UpdateGroupRequest;
use App\Domains\ConversationModule\Services\GroupService;
use Illuminate\Http\Request;

class GroupConversationController extends Controller
{
    public function getAll()
    {
        return GroupService::getAll();
    }

    public function getGroup($group_id){
        return GroupService::getGroup($group_id);
    }

    public function create(CreateGroupRequest $request)
    {
        return GroupService::createGroup($request);
    }

    public function update(UpdateGroupRequest $request, $group_id)
    {
        return GroupService::update($group_id, $request);
    }

    public function delete($group_id)
    {
        return GroupService::delete($group_id);
    }

    public function getAllParticipants($group_id)
    {
        return GroupService::getAllParticipants($group_id);
    }

    public function addMember(AddGroupParticipationRequest $request, $group_id)
    {
        return GroupService::addMember($group_id, $request->input('userIds'));
    }

    public function removeMember($user_id, $group_id)
    {
        return GroupService::removeMember($group_id, $user_id);
    }
}
