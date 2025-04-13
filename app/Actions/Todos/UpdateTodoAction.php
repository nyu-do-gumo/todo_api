<?php

namespace App\Actions\Todos;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateTodoAction
{
    /**
     * Todoの更新処理
     *
     * @param User $user 認証済みユーザー
     * @param int $todoId 更新するTodoのID
     * @param array $data 更新データ
     * @return Todo
     * @throws AuthorizationException|ValidationException
     */
    public function execute(User $user, int $todoId, array $data): Todo
    {
        // Todo取得
        $todo = Todo::findOrFail($todoId);

        // 他人のTodoは更新できない
        if ($todo->user_id !== $user->id) {
            throw new AuthorizationException('Forbidden');
        }

        // バリデーション
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // 更新実行
        $todo->update([
            'title' => $data['title'],
        ]);

        return $todo;
    }
}