<?php

namespace App\Domains\ConversationModule\Repositories;

use App\Domains\ConversationModule\Models\Group;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;

class GroupRepository
{
    
    protected static $firebaseStorage;

    public static function initFirebase()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env("FIREBASE_SERVICE_ACCOUNT")))
            ->withProjectId(env('FIREBASE_PROJECT_ID'));

        self::$firebaseStorage = $firebase->createStorage();
    }
    public static function create($data)
    {
        return Group::create($data);
    }

    public static function update($groupId, array $data)
    {
        $group = Group::find($groupId);

        if (!$group) {
            return null;
        }

        DB::transaction(function () use ($group, $data) {
            $group->update($data);
        });

        return $group;
    }

    public static function deleteGroupConversation($group_id)
    {
        $group = Group::find($group_id);
        if (!$group) {
            return false; // Group not found
        }

        $conversation = $group->conversation;
        if ($conversation) {
          ConversationRepository::deleteConversation($conversation->id);
        }

        $group->delete();
        return true;
    }

    public static function getAllGroupConversations()
    {
        return Group::all();
    }

    public static function getGroupConversationById($group_id)
    {
        return Group::find($group_id);
    }

    public static function addMemberToGroup($group_id, $user_ids)
    {
        $group = Group::find($group_id);
        if (!$group) {
            return false; // Group not found
        }

        $group->members()->attach($user_ids);

        return true;
    }

    public static function removeMemberFromGroup($group_id, $user_id)
    {
        $group = Group::find($group_id);
        if (!$group) {
            return false; // Group not found
        }

        $group->members()->detach($user_id);

        return true;
    }

    public static function getAllParticipants($group_id)
    {
        $group = Group::find($group_id);
        if (!$group) {
            return null; // Group not found
        }

        return $group->members;
    }
    
}
