<?php

namespace App\Actions\Todos;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class ToggleTodoAction
{
    /**
     * Todo完了状態のトグル処理
     *
     * @param User $user 認証済みユーザー
     * @param int $todoId トグルするTodoのID
     * @return Todo
     * @throws AuthorizationException
     */
    public function execute(User $user, int $todoId): Todo
    {
        // Todo取得
        $todo = Todo::findOrFail($todoId);

        // 他人のTodoはトグルできない
        if ($todo->user_id !== $user->id) {
            throw new AuthorizationException('Forbidden');
        }

        // トグル実行
        $todo->toggleAsCompleted();

        return $todo;
    }
}