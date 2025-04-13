<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // すべての例外に対するグローバルハンドラーを追加
        $exceptions->render(function (\Throwable $e, $request) {
            // 認証の例外をチェック
            if ($e instanceof AuthenticationException ) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            // それ以外はデフォルト処理
            return null;
        });
    })->create();
