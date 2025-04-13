<?php

namespace Tests\Feature\Api;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    // 認証済みユーザーを準備するプライベートメソッド
    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }

    #[Test]
    public function unauthenticated_user_cannot_access_todos()
    {
        // 認証なしでAPIを叩くと401が返ってくるはず
        $response = $this->getJson('/api/todos');
        $response->assertStatus(401);
    }

    #[Test]
    public function user_can_get_all_own_todos()
    {
        // 認証済みユーザーを準備
        $user = $this->authenticateUser();

        // このユーザーのTodoを3つ作成
        $todos = Todo::factory()->count(3)->create(['user_id' => $user->id]);

        // 別ユーザーのTodoも1つ作成（これは取得されないはず）
        $otherUser = User::factory()->create();
        Todo::factory()->create(['user_id' => $otherUser->id]);

        // APIを呼び出し
        $response = $this->getJson('/api/todos');

        // dd($response->getContent());

        // 3件のTodoのみ取得できることを確認
        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'completed', 'completed_at', 'user_id', 'created_at', 'updated_at']
                     ]
                 ]);
    }

    #[Test]
    public function user_can_get_single_todo()
    {
        // 認証済みユーザーを準備
        $user = $this->authenticateUser();

        // テスト用のTodoを作成
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // APIを呼び出し
        $response = $this->getJson("/api/todos/{$todo->id}");

        // 正しいTodoが取得できることを確認
        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $todo->id,
                         'title' => $todo->title,
                         'completed' => $todo->completed,
                     ]
                 ]);
    }

    #[Test]
    public function user_cannot_get_other_users_todo()
    {
        // 認証済みユーザーを準備
        $user = $this->authenticateUser();

        // 別ユーザーのTodoを作成
        $otherUser = User::factory()->create();
        $otherTodo = Todo::factory()->create(['user_id' => $otherUser->id]);

        // APIを呼び出し
        $response = $this->getJson("/api/todos/{$otherTodo->id}");

        // 403 Forbiddenが返ることを確認
        $response->assertStatus(403);
    }

    #[Test]
    public function user_can_create_todo()
    {
        // 認証済みユーザーを準備
        $user = $this->authenticateUser();

        // 新規Todo用のデータ
        $todoData = [
            'title' => 'テスト用Todo',
        ];

        // APIを呼び出し
        $response = $this->postJson('/api/todos', $todoData);

        // レスポンスとDBを確認
        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                         'title' => 'テスト用Todo',
                         'completed' => false,
                     ]
                 ]);

        $this->assertDatabaseHas('todos', [
            'title' => 'テスト用Todo',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function user_can_update_todo()
    {
        // 認証済みユーザーを準備
        $user = $this->authenticateUser();

        // テスト用のTodoを作成
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // 更新用データ
        $updateData = [
            'title' => '更新後のタイトル',
        ];

        // APIを呼び出し
        $response = $this->putJson("/api/todos/{$todo->id}", $updateData);

        // レスポンスとDBを確認
        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $todo->id,
                         'title' => '更新後のタイトル',
                     ]
                 ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => '更新後のタイトル',
        ]);
    }

    #[Test]
    public function user_can_delete_todo()
    {
        // 認証済みユーザーを準備
        $user = $this->authenticateUser();

        // テスト用のTodoを作成
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // APIを呼び出し
        $response = $this->deleteJson("/api/todos/{$todo->id}");

        // レスポンスとDBを確認
        $response->assertStatus(204); // No Content
        $this->assertModelMissing($todo);
    }

    #[Test]
    public function user_can_toggle_todo_completion()
    {
        // 認証済みユーザーを準備
        $user = $this->authenticateUser();

        // 未完了のTodoを作成
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed' => false,
            'completed_at' => null
        ]);

        // トグルAPIを呼び出し
        $response = $this->putJson("/api/todos/{$todo->id}/toggle");

        // 完了状態になったことを確認
        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $todo->id,
                         'completed' => true,
                     ]
                 ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed' => true,
        ]);
        $this->assertNotNull(Todo::find($todo->id)->completed_at);

        // もう一度トグルして未完了に戻す
        $response = $this->putJson("/api/todos/{$todo->id}/toggle");

        // 未完了状態になったことを確認
        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $todo->id,
                         'completed' => false,
                     ]
                 ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed' => false,
        ]);
        $this->assertNull(Todo::find($todo->id)->completed_at);
    }
}