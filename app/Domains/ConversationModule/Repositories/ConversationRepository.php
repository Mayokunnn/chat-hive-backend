<?php

namespace App\Domains\ConversationModule\Repositories;

use App\Domains\ConversationModule\Models\Conversation;
use App\Domains\UserModule\Models\User;
use Kreait\Firebase\Factory;

class ConversationRepository
{


    protected static $firebaseStorage;

    public static function initFirebase()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env("FIREBASE_SERVICE_ACCOUNT")))
            ->withProjectId(env('FIREBASE_PROJECT_ID'));

        self::$firebaseStorage = $firebase->createStorage();
    }

    public static function getAllConversations()
    {
        return Conversation::all();
    }

 

    public static function getConversationById($id)
    {
        $conversation = Conversation::find($id);

        return $conversation;
    }


    public static function createPersonalConversation(array $userIds, $name = null, $image = null)
    {

        self::initFirebase();

        $conversation = Conversation::create([
            'name' => $name,
            'image' => null, // Temporarily set to null until we have the URL
        ]);

        // Attach users to the conversation
        $conversation->users()->attach($userIds);

        if ($image) {
            $fileName = 'images/' . time() . '_' . $image->getClientOriginalName();
            $localPath = $image->getPathname();

            $bucket = self::$firebaseStorage->getBucket();
            $bucket->upload(fopen($localPath, 'r'), [
                'name' => $fileName
            ]);

            $imageUrl = $bucket->object($fileName)->signedUrl(new \DateTime('9999-12-31'));

            $conversation->image = $imageUrl;
            $conversation->save();
        }

        return $conversation;
    }


    public static function updateConversation($conversation_id, $request)
    {
        self::initFirebase();
        $conversation = Conversation::find($conversation_id);
        $conversation->name = $request->input('name') ?? $conversation->name;

        if ($request->hasFile('file')) {
            $image = $request->file('image');
            // Delete the existing image from Firebase Storage
            $existingImageUrl = $conversation->image;
            if ($existingImageUrl) {
                $existingFileName = basename(parse_url($existingImageUrl, PHP_URL_PATH));
                $bucket = self::$firebaseStorage->getBucket();
                $bucket->object($existingFileName)->delete();
            }

            // Upload the new image to Firebase Storage
            $fileName = 'images/' . time() . '_' . $image->getClientOriginalName();
            $localPath = $image->getPathname();

            $bucket = self::$firebaseStorage->getBucket();
            $bucket->upload(fopen($localPath, 'r'), [
                'name' => $fileName
            ]);

            $imageUrl = $bucket->object($fileName)->signedUrl(new \DateTime('9999-12-31'));

            $conversation->image = $imageUrl;
        }

        $conversation->save();
        return $conversation;
    }

    public static function deleteConversation($conversation_id)
    {
        self::initFirebase();

        $conversation = Conversation::find($conversation_id);
        if (!$conversation) {
            return false; // Conversation not found
        }

        // Delete the image from Firebase Storage
        $imageUrl = $conversation->image;
        if ($imageUrl) {
            $fileName = basename(parse_url($imageUrl, PHP_URL_PATH));
            $bucket = self::$firebaseStorage->getBucket();
            $bucket->object($fileName)->delete();
        }

        // Delete the conversation from the database
        $conversation->delete();
        return true;
    }

    public static function addUserToConversation($conversation_id, $userId)
    {
        $conversation = Conversation::find($conversation_id);
        $conversation->users()->attach($userId);

        $conversation->save();
        return $conversation;
    }

    public static function removeUserFromConversation($conversation_id, $userId)
    {
        $conversation = Conversation::find($conversation_id);
        $conversation->users()->detach($userId);
    }


    public static function getAllUsersInConversation($conversation_id)
    {
        $conversation = Conversation::find($conversation_id);
        return $conversation->users;
    }

    public static function createGroupConversation($name = null, $image = null)
    {

        self::initFirebase();

        $conversation = Conversation::create([
            'name' => $name,
            'image' => null, // Temporarily set to null until we have the URL
        ]);

        if ($image) {
            $fileName = 'images/' . time() . '_' . $image->getClientOriginalName();
            $localPath = $image->getPathname();

            $bucket = self::$firebaseStorage->getBucket();
            $bucket->upload(fopen($localPath, 'r'), [
                'name' => $fileName
            ]);

            $imageUrl = $bucket->object($fileName)->signedUrl(new \DateTime('9999-12-31'));

            $conversation->image = $imageUrl;
            $conversation->save();
        }

        return $conversation;
    }
}
