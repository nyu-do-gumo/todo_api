<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー未認証状態で /api/logout にアクセスし、401 (Unauthorized) が返ることを確認するテスト
     */
    public function test_logout_no_token()
    {
        // 認証トークンが渡されない状態でlogoutリクエストを送信
        $response = $this->postJson('/api/logout');

        // 401 Unauthorized が返ることを確認
        $response->assertStatus(401);
    }

    /**
     * 発行したトークンを使用して /api/logout にアクセスし、
     * ログアウト後にそのトークンでは /api/user にアクセスできず、401 (Unauthorized) が返ることを確認するテスト
     */
    public function test_logout_with_valid_token()
    {
        // まずユーザーを作成（DBに保存）
        $user = User::factory()->create();

        // ユーザーのパーソナルアクセストークンを発行
        $tokenResult = $user->createToken('default');
        $plainTextToken = $tokenResult->plainTextToken;

        // 発行したトークンを Authorization ヘッダーに設定し、/api/logout にPOSTリクエストを送信する
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainTextToken,
        ])->postJson('/api/logout');

        // /api/logout のレスポンスを200 OKとし、"Logged out successfully" のメッセージが返されることを確認
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Logged out successfully',
                 ]);

        // トークン削除がDB上で行われたか確認
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'default',
        ]);

    }
}
