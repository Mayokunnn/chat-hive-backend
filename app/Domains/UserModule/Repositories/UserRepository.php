<?php

namespace App\Domains\UserModule\Repositories;

use App\Domains\UserModule\Models\LoginAttempt;
use App\Domains\UserModule\Models\User;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Factory;

class UserRepository
{
    protected static $firebaseStorage;

    protected static $decayMinutes;

    public static function initFirebase()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env("FIREBASE_SERVICE_ACCOUNT")))
            ->withProjectId(env('FIREBASE_PROJECT_ID'));

        self::$firebaseStorage = $firebase->createStorage();
    }

    public static function setUp()
    {
        self::$decayMinutes = intval(config('auth.decayminutes'));
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

    public static function updateUser($id, $request)
    {
        self::initFirebase();
        $user = self::getUserById($id);
        $user->name = $request->input('name') ?? $user->name;
        $user->email = $request->input('email') ?? $user->email;
        $user->username = $request->input('username') ?? $user->username;

        if ($request->hasFile('image')) {
            // Delete the existing image from Firebase Storage
            $existingImageUrl = $user->image;
            if ($existingImageUrl) {
                $existingFileName = basename(parse_url($existingImageUrl, PHP_URL_PATH));
                $bucket = self::$firebaseStorage->getBucket();
                $bucket->object($existingFileName)->delete();
            }

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

    public static function deleteUser($id)
    {
        self::initFirebase();
        $user = self::getUserById($id);


        // Delete the existing image from Firebase Storage
        $existingImageUrl = $user->image;
        if ($existingImageUrl) {
            $existingFileName = basename(parse_url($existingImageUrl, PHP_URL_PATH));
            $bucket = self::$firebaseStorage->getBucket();
            $bucket->object($existingFileName)->delete();
        }


        $user->delete();

        return true;
    }

    public static function incrementLoginAttempts($user)
    {
        self::setUp();
        if ($user) {
            $loginAttempt = LoginAttempt::firstOrCreate(
                ['user_id' => $user->id],
                ['attempts' => 0, 'last_attempt_at' => now()]
            );

            if (now()->diffInMinutes($loginAttempt->last_attempt_at) > self::$decayMinutes) {
                $loginAttempt->attempts = 1;
            } else {
                $loginAttempt->increment('attempts');
            }

            $loginAttempt->last_attempt_at = now();
            $loginAttempt->save();
        }
    }

    public static function resetLoginAttempts($user)
    {
        if ($user) {
            $loginAttempt = LoginAttempt::firstOrCreate(
                ['user_id' => $user->id],
                ['attempts' => 0]
            );

            $loginAttempt->attempts = 0;
            $loginAttempt->last_attempt_at = null;
            $loginAttempt->save();
        }
    }


    public static function getLoginAttempts($user)
    {
        $maxAttempts = config('auth.max_attempts', 5);

        if ($user) {
            $loginAttempt = LoginAttempt::where('user_id', $user->id)->first();
            $attempts = $loginAttempt ? $loginAttempt->attempts : 0;

            return $attempts;
        }

        return $maxAttempts;
    }

    public static function getRemainingLoginAttempts($user)
    {
        $maxAttempts = config('auth.max_attempts', 5);

        if ($user) {
            $loginAttempt = LoginAttempt::where('user_id', $user->id)->first();
            $attempts = $loginAttempt ? $loginAttempt->attempts : 0;

            return $maxAttempts - $attempts;
        }

        return $maxAttempts;
    }




    public static  function getUserByUsername($username)
    {
        $user = User::where("username", $username)->first();
        return $user;
    }

    public static  function getUserById($id)
    {
        $user = User::where("id", $id)->first();
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
