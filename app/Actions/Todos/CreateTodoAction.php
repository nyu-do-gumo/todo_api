<?php

namespace App\Actions\Todos;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateTodoAction
{
    /**
     * 新しいTodoの作成処理
     *
     * @param User $user 認証済みユーザー
     * @param array $data Todo作成用データ
     * @return Todo
     * @throws ValidationException
     */
    public function execute(User $user, array $data): Todo
    {
        // バリデーション
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Todoの作成
        return $user->todos()->create([
            'title' => $data['title'],
            'completed' => false,
        ]);
    }
}