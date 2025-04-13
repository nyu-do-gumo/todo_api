<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性（これ設定しないとcreateとかfillとかで代入できないよ）
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'completed',
        'completed_at',
        'user_id'
    ];

    /**
     * 型変換の定義（DBから取得したデータを自動で型変換する）
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed' => 'boolean', // DBのtinyintをPHPのbooleanに変換
        'completed_at' => 'datetime', // 文字列をCarbonオブジェクトに変換
    ];

    /**
     * Todoの所有ユーザーを取得するリレーション定義
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Todoを完了 <> 実行中 状態にトグルするメソッド
     */
    public function toggleAsCompleted(): void
    {
        $this->completed = !$this->completed; // 現在の状態を反転
        if ($this->completed) {
            $this->completed_at = now();
        } else {
            $this->completed_at = null;
        }
        $this->save();
    }
}
