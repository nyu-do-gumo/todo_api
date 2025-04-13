<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Api\Auth\LoginAction;
use App\Actions\Api\Auth\LogoutAction;
use App\Actions\Api\Auth\RegisterAction;

class AuthController extends Controller
{
    public function login(LoginAction $loginAction, Request $request)
    {
        return $loginAction->execute($request);
    }

    public function logout(LogoutAction $logoutAction, Request $request)
    {
        return $logoutAction->execute($request);
    }

    public function register(RegisterAction $registerAction, Request $request)
    {
        return $registerAction->execute($request);
    }
}