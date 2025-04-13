<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 認証失敗時のリダイレクト先をカスタマイズ
        // ここでnullを返すとリダイレクト処理がスキップされ、
        // APIリクエストでも常にJSON形式の401レスポンスが返る
        Authenticate::redirectUsing(function ($request) {
            if($request->is('api/*') || $request->expectsJson()) {
                return null;
            }
            // それ以外の場合は必要に応じて独自のリダイレクト先を返せる
            // api.loginが妥当かは議論の余地がある
            return route('api.login');
        });
    }
}
