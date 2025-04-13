<?php

namespace App\Actions\Api\Auth;

use Illuminate\Http\Request;

class LogoutAction
{
    public function execute(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }
}