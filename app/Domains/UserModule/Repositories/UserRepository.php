<?php

namespace App\Domains\UserModule\Repositories;

use App\Domains\UserModule\Models\User;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Factory;

class UserRepository
{
    protected static $firebaseStorage;

    public static function initFirebase()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env("FIREBASE_SERVICE_ACCOUNT")))
            ->withProjectId(env('FIREBASE_PROJECT_ID'));

        self::$firebaseStorage = $firebase->createStorage();
    }
    public static function createUser($request)
    {
        self::initFirebase();
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '.' . $image->getClientOriginalExtension();
            $localPath = $image->getPathname();

            $bucket = self::$firebaseStorage->getBucket();
            $bucket->upload(fopen($localPath, 'r'), [
                'name' => $fileName
            ]);

            $imageReference = $bucket->object($fileName);
            $imageUrl = $imageReference->signedUrl(new \DateTime('9999-12-31'));

            $user->image = $imageUrl;
        }


        $user->save();

        return $user;
    }
    public static  function getUserByUsername($username)
    {
        $user = User::where("username", $username)->first();
        return $user;
    }

    public static function getUserByEmail($email)
    {
        $user = User::where("email", $email)->first();
        return $user;
    }

    public static function updateUserLoginStatus($email, $status)
    {
        $user = User::where("email", $email)->first();
        $user->loggedIn = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $user->save();
    }

    public static function getAllConversationsOfAUser($user_id)
    {
        $user = User::find($user_id);
        $conversations = $user->conversations;

        return $conversations;
    }
}
