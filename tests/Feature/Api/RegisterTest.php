<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 有効なデータでユーザー登録できることをテスト
     */
    public function test_user_can_register_with_valid_data()
    {
        // テスト用のユーザーデータ作成
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // APIエンドポイントにPOSTリクエスト送信
        $response = $this->postJson('/api/register', $userData);

        // レスポンスのステータスとJSONの構造をチェック
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'created_at',
                         'updated_at',
                     ],
                     'token'
                 ]);

        // DBにユーザーが保存されてること確認
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'テストユーザー'
        ]);
    }

    /**
     * 必須項目が欠けてる場合にエラーが返ってくることをテスト
     */
    public function test_user_cannot_register_with_invalid_data()
    {
        // 空のデータでリクエスト送信（絶対失敗する）
        $response = $this->postJson('/api/register', []);

        // 422 Unprocessable Entityが返ること確認
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);

        // パスワード確認が一致しないケース
        $response = $this->postJson('/api/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password', // わざと違うパスワード入れてみる
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /**
     * 既存のメールアドレスでは登録できないことをテスト
     */
    public function test_user_cannot_register_with_existing_email()
    {
        // 既存ユーザーを作成しておく
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // 同じメアドで登録しようとしてみる
        $userData = [
            'name' => '新規ユーザー',
            'email' => 'existing@example.com', // ← これが既に使われてるメルアド
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        // メアドの一意性エラーを確認
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}