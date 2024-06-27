<?php

namespace App\Domains\UserModule\Services;

use App\Domains\UserModule\Repositories\UserRepository;
use App\Domains\UserModule\Resources\UserResource;
use App\Traits\ResponseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AuthService
{
    public static function login($request)
    {
        try {

            $credentials = request(['email', 'password']);
            $user = UserRepository::getUserByEmail($request->input('email'));

            if (empty($user)) {
                return ResponseService::error('Request Error: User not found', [], 400);
            }

            // if (intval($user->loggedIn) == 1) {
            //     return ResponseService::error('You are already logged in', [auth()->user()], 422);
            // }

            $expires = intval(config('jwt.ttl'));
            $token = Auth::setTTL($expires)->attempt($credentials);

            if (empty($token)) {
                return ResponseService::error('Your credentials are wrong', [], 422);
            }

            $user->loggedIn = 1;
            UserRepository::updateUserLoginStatus($user->email, 1);

            // construct response data structure
            $responseData = [
                'token' => $token,
                'token_type' => 'bearer',
                'user' => new UserResource($user),
                'expires_in' => $expires * 60
            ];

            return ResponseService::success('Login successful', $responseData);
        } catch (\Exception $ex) {
            Log::error($request['username'] . " login failed with  {$ex->getMessage()} {$ex->getFile()} on line {$ex->getLine()}", [$ex]);
            return ResponseService::error("Server Error: login failed", [$ex->getMessage()], 500);
        }
    }

    public static function register($request)
    {
        try {
            $credentials = request(['email', 'password']);

            $user = UserRepository::getUserByEmail($request->input('email'));

            if (!empty($user)) {
                return ResponseService::error('There is a user with that email', [], 400);
            }

            $user = UserRepository::createUser($request);



            $expires = intval(config('jwt.ttl'));
            $token = Auth::setTTL($expires)->attempt($credentials);

            if (empty($token)) {
                return ResponseService::error('Your credentials are wrong', [], 422);
            }

            $user->loggedIn = 1;
            UserRepository::updateUserLoginStatus($user->email, 1);

            $responseData = [
                'token' => $token,
                'token_type' => 'bearer',
                'user' => new UserResource($user),
                'expires_in' => $expires * 60,
            ];

            return ResponseService::success('You have been registered successfully', [$responseData]);
        } catch (\Exception $ex) {
            Log::error($request['username'] . " login failed with  {$ex->getMessage()} {$ex->getFile()} on line {$ex->getLine()}", [$ex]);
            return ResponseService::error("Server Error: registration failed", [$ex->getMessage()], 500);
        }
    }

    public static function logout()
    {
        $user = Auth::user();
        UserRepository::updateUserLoginStatus($user->email, 0);

        Auth::logout();

        return ResponseService::success("Logout successful", [], 200);
    }

    public static function refreshToken()
    {
        $expires = intval(config('jwt.ttl'));
        $refresh = Auth::setTTL($expires)->refresh();

        $data = [
            'token' => $refresh,
            'token_type' => 'bearer',
            'expires_in' => $expires * 60
        ];

        return ResponseService::success(200, "Token Refreshed Successful", $data);
    }

    public static function resetPassword($request)
    {
        self::validate($request, [
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return ResponseService::success('Password reset link sent', []);
        }

        return ResponseService::error('Unable to send password reset link', [], 500);
    }

    public static function changePassword($request, $userId)
    {

        $user = UserRepository::getUserById($userId);

        if (!Hash::check($request->input('currentPassword'), $user->password)) {
            return ResponseService::error('Current password is incorrect', [], 400);
        }

        $user->password = Hash::make($request->input('newPassword'));
        $user->save();

        self::logout();

        return ResponseService::success('Password changed successfully', []);
    }

    private static function validate($request, $rules)
    {
        $validator = app('validator')->make($request->all(), $rules);
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
