<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Api\Auth\LoginAction;

class AuthController extends Controller
{
    public function login(LoginAction $loginAction, Request $request)
    {
        return $loginAction->execute($request);
    }
}