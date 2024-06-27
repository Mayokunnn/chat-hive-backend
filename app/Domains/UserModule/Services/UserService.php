<?php

namespace App\Domains\UserModule\Services;

use App\Domains\UserModule\Repositories\UserRepository;
use App\Domains\UserModule\Resources\UserResource;
use App\Traits\ResponseService;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public static function  getUser($id)
    {
        try {
            $user = UserRepository::getUserById($id);

            if (!$user || empty($user)) {
                return ResponseService::error("Request Error: User does not exist", [], 400);
            }

            return ResponseService::success('Success', new UserResource($user), 200);
        } catch (Exception $e) {
            return ResponseService::error("Server Error: User could not be found", [], 500);
        }
    }

    public static function  updateUser($id, $request)
    {
        try {
            $user = UserRepository::getUserById($id);

            if (!$user || empty($user)) {
                return ResponseService::error("Request Error: User does not exist", [], 400);
            }

            $user = UserRepository::updateUser($id, $request);

            return ResponseService::success('Success: User updated', new UserResource($user), 200);
        } catch (Exception $e) {
            return ResponseService::error("Server Error: User could not be found", [], 500);
        }
    }

    public static function deleteUser($id)
    {
        try {
            $user = Auth::user();
            UserRepository::updateUserLoginStatus($user->email, 0);

            Auth::logout();

            UserRepository::deleteUser($id);

            return ResponseService::success("Account deleted", [], 200);
        } catch (Exception $e) {
            return ResponseService::error("Server Error: User could not be deleted", [], 500);
        }
    }
}
