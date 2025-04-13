<?php

namespace App\Actions\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterAction
{
    /**
     * ユーザー登録処理を実行する
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function execute(Request $request)
    {
        // dump('RegisterAction::execute()');
        // バリデーション実行
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        // dump('green validated');
        // dd($validated);

        // ユーザー作成
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // トークン発行
        $token = $user->createToken('auth_token')->plainTextToken;
        // dump($token);
        // dd($user->tokens);

        // レスポンス返却
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }
}