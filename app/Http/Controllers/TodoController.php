<?php

namespace App\Http\Controllers;

use App\Actions\Todos\CreateTodoAction;
use App\Actions\Todos\DeleteTodoAction;
use App\Actions\Todos\GetTodoAction;
use App\Actions\Todos\GetTodosAction;
use App\Actions\Todos\ToggleTodoAction;
use App\Actions\Todos\UpdateTodoAction;
use App\Http\Resources\TodoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TodoController extends Controller
{
    /**
     * Todo一覧取得
     *
     * @param GetTodosAction $action
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(GetTodosAction $action, Request $request): AnonymousResourceCollection
    {
        $todos = $action->execute($request->user());
        return TodoResource::collection($todos);
    }

    /**
     * 個別Todo取得
     *
     * @param GetTodoAction $action
     * @param Request $request
     * @param int $id
     * @return TodoResource
     */
    public function show(GetTodoAction $action, Request $request, int $id): TodoResource
    {
        $todo = $action->execute($request->user(), $id);
        return new TodoResource($todo);
    }

    /**
     * Todo新規作成
     *
     * @param CreateTodoAction $action
     * @param Request $request
     * @return TodoResource
     */
    public function store(CreateTodoAction $action, Request $request): JsonResponse
    {
        $todo = $action->execute($request->user(), $request->all());
        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    /**
     * Todo更新
     *
     * @param UpdateTodoAction $action
     * @param Request $request
     * @param int $id
     * @return TodoResource
     */
    public function update(UpdateTodoAction $action, Request $request, int $id): TodoResource
    {
        $todo = $action->execute($request->user(), $id, $request->all());
        return new TodoResource($todo);
    }

    /**
     * Todo削除
     *
     * @param DeleteTodoAction $action
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function destroy(DeleteTodoAction $action, Request $request, int $id): Response
    {
        $action->execute($request->user(), $id);
        return response()->noContent(); // 204 No Content
    }

    /**
     * Todo完了状態のトグル
     *
     * @param ToggleTodoAction $action
     * @param Request $request
     * @param int $id
     * @return TodoResource
     */
    public function toggle(ToggleTodoAction $action, Request $request, int $id): TodoResource
    {
        $todo = $action->execute($request->user(), $id);
        return new TodoResource($todo);
    }
}