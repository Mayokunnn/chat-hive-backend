<?php

namespace App\Http\Controllers;

use App\Domains\UserModule\Services\AuthService;

use App\Domains\UserModule\Requests\LoginRequest;
use App\Domains\UserModule\Requests\RegisterRequest;


class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
      return AuthService::login($request);
    }

    public function register(RegisterRequest $request)
    {
       return AuthService::register($request);
    }
    public function logout()
    {
       return AuthService::logout();
    }

    public function refreshToken(){
       return AuthService::refreshToken();
    }
    //
}
