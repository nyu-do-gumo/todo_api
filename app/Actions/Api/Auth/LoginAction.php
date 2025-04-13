<?php

namespace App\Actions\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginAction
{
    public function execute(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $request->user();
        $tokenName = $request->input('token_name', 'default');

        $token = $user->createToken($tokenName);

        return response()->json(['token' => $token->plainTextToken]);
    }
}