<?php

namespace App\Actions\Todos;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class DeleteTodoAction
{
    /**
     * Todo削除処理
     *
     * @param User $user 認証済みユーザー
     * @param int $todoId 削除するTodoのID
     * @return bool
     * @throws AuthorizationException
     */
    public function execute(User $user, int $todoId): bool
    {
        // Todo取得
        $todo = Todo::findOrFail($todoId);

        // 他人のTodoは削除できない
        if ($todo->user_id !== $user->id) {
            throw new AuthorizationException('Forbidden');
        }

        // 削除実行
        return $todo->delete();
    }
}