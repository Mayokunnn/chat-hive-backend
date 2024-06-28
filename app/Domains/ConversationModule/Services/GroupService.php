<?php

namespace App\Domains\ConversationModule\Services;

use App\Domains\ConversationModule\Repositories\GroupRepository;
use App\Domains\ConversationModule\Repositories\ConversationRepository;
use App\Domains\ConversationModule\Resources\GroupResource;
use App\Domains\UserModule\Repositories\UserRepository;
use App\Domains\UserModule\Resources\UserResource;
use App\Traits\ResponseService;
use Exception;

class GroupService
{
    public static function getAll()
    {
        $groups = GroupRepository::getAllGroupConversations();
        return ResponseService::success('Group conversations retrieved successfully', GroupResource::collection($groups), 200);
    }

    public static function getGroup($id)
    {
        try {
            $group = GroupRepository::getGroupConversationById($id);

            if (!$group) {
                return ResponseService::error('Group conversation not found', [], 404);
            }

            return ResponseService::success('Group conversation retrieved successfully', new GroupResource($group), 200);
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to create group', [], 500);
        }
    }
    public static function createGroup($request)
    {
        try {
            $conversation = ConversationRepository::createGroupConversation($request->name, $request->file('image'));


            $group = GroupRepository::create([
                'name' => $conversation->name,
                'owner_id' => auth()->user()->id,
                'conversation_id' => $conversation->id
            ]);

            $group->members()->attach(auth()->user()->id);

            return ResponseService::success('Group created successfully', new GroupResource($group), 200);
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to create group', [], 500);
        }
    }

    public static function update($groupId, $request)
    {
        try {
            $group = GroupRepository::getGroupConversationById($groupId);

            if (!$group) {
                return ResponseService::error('Group conversation not found', [], 404);
            }

            $conversation = ConversationRepository::updateConversation($group->conversation->id, $request);

            if (!$conversation) {

                return ResponseService::error('Conversation not found or could not be updated', [], 404);
            }

            $UpdatedGroup = GroupRepository::update($groupId, $request->all());

            return ResponseService::success('Group conversation updated successfully', new GroupResource($UpdatedGroup), 200);
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to update group', [], 500);
        }
    }

    public static function delete($group_id)
    {
        try {
            $deleted = GroupRepository::deleteGroupConversation($group_id);
            if ($deleted) {
                return ResponseService::success('Group conversation deleted successfully', [], 200);
            } else {
                return ResponseService::error('Request Error: Group conversation not found', [], 404);
            }
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to delete group conversation', [], 500);
        }
    }

    public static function getAllParticipants($group_id)
    {
        try {
            $participants = GroupRepository::getAllParticipants($group_id);
            if ($participants === null) {
                return ResponseService::error('Request Error: Group not found', [], 404);
            }
            return ResponseService::success('Participants retrieved successfully', UserResource::collection($participants), 200);
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to retrieve participants', [], 500);
        }
    }

    public static function addMember($group_id, $user_ids)
    {
        try {
            $added = GroupRepository::addMemberToGroup($group_id, $user_ids);
            if ($added) {
                return ResponseService::success('Members added successfully', [], 200);
            } else {
                return ResponseService::error('Request Error: Group not found', [], 404);
            }
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to add members', [], 500);
        }
    }

    public static function removeMember($group_id, $user_id)
    {
        try {
            $removed = GroupRepository::removeMemberFromGroup($group_id, $user_id);
            if ($removed) {
                return ResponseService::success('Member removed successfully', [], 200);
            } else {
                return ResponseService::error('Request Error: Group or member not found', [], 404);
            }
        } catch (Exception $e) {
            return ResponseService::error('Server Error: Failed to remove member', [], 500);
        }
    }
}
