<?php

namespace App\Actions\Todos;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GetTodosAction
{
    /**
     * 指定ユーザーのTodo一覧を取得
     *
     * @param User $user 認証済みユーザー
     * @return Collection
     */
    public function execute(User $user): Collection
    {
        // ログインユーザーのTodo一覧を取得する
        return $user->todos()->latest()->get();
    }
}