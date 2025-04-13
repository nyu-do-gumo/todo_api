<?php

namespace App\Actions\Todos;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class GetTodoAction
{
    /**
     * 指定IDのTodoを取得（認証チェック付き）
     *
     * @param User $user 認証済みユーザー
     * @param int $todoId 取得するTodoのID
     * @return Todo
     * @throws AuthorizationException
     */
    public function execute(User $user, int $todoId): Todo
    {
        // Todo取得
        $todo = Todo::findOrFail($todoId);

        // 他人のTodoは見れない
        if ($todo->user_id !== $user->id) {
            throw new AuthorizationException('Forbidden');
        }

        return $todo;
    }
}