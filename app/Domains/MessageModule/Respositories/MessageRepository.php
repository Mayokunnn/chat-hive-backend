<?php

namespace App\Domains\MessageModule\Respositories;

use App\Domains\MessageModule\Models\Message;
use Kreait\Firebase\Factory;

class MessageRepository
{

    protected static $firebaseStorage;

    public static function initFirebase()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env("FIREBASE_SERVICE_ACCOUNT")))
            ->withProjectId(env('FIREBASE_PROJECT_ID'));

        self::$firebaseStorage = $firebase->createStorage();
    }

    public static function create($request)
    {
        self::initFirebase();
        $message = new Message();
        $message->conversation_id = $request->input('conversation_id');
        $message->sender_id = $request->input('sender_id');
        $message->type = $request->input('type');
        $message->content = $request->input('content') ?? '';
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'uploads/' . time() . '.' . $file->getClientOriginalExtension();
            $localPath = $file->getPathname();

            $bucket = self::$firebaseStorage->getBucket();
            $bucket->upload(fopen($localPath, 'r'), [
                'name' => $fileName
            ]);

            $message->url = $bucket->object($fileName)->signedUrl(new \DateTime('9999-12-31'));

            $message->save();

            return $message;
        }
    }

    public static function getMessagesInConversation($conversation_id)
    {
        return Message::where('conversation_id', $conversation_id)->get();
    }

    public static function getMessageById($message_id)
    {
        return Message::find($message_id);
    }

    public static function updateMessage($message_id, $data, $request)
    {
        self::initFirebase();
        $message = Message::find($message_id);
        $message->fill($data); // Update other fields

        if ($request->hasFile('file')) {
            // Delete the existing file from Firebase Storage
            $existingUrl = $message->url;
            if ($existingUrl) {
                $existingFileName = basename(parse_url($existingUrl, PHP_URL_PATH));
                $bucket = self::$firebaseStorage->getBucket();
                $bucket->object($existingFileName)->delete();
            }

            // Upload the new file to Firebase Storage
            $file = $request->file('file');
            $fileName = 'uploads/' . time() . '.' . $file->getClientOriginalExtension();
            $localPath = $file->getPathname();

            $bucket = self::$firebaseStorage->getBucket();
            $bucket->upload(fopen($localPath, 'r'), [
                'name' => $fileName
            ]);

            $message->url = $bucket->object($fileName)->signedUrl(new \DateTime('9999-12-31'));
        }

        $message->save();
        return $message;
    }


    public static function deleteMessage($message_id)
    {
        self::initFirebase();
        $message = Message::find($message_id);

        // Delete the file from Firebase Storage
        $url = $message->url;
        if ($url) {
            $fileName = basename(parse_url($url, PHP_URL_PATH));
            $bucket = self::$firebaseStorage->getBucket();
            $bucket->object($fileName)->delete();
        }

        // Delete the message from the database
        $message->delete();
    }


    public static function getMessagesSentByUser($user_id)
    {
        return Message::where('sender_id', $user_id)->get();
    }
}
