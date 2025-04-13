<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Todo::class;

    /**
     * テスト用のダミーTodoデータ定義💪
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // ユーザーも自動生成（超便利✨）
            'title' => $this->faker->sentence(6), // ランダムなタイトル生成
            'completed' => false, // デフォルトは未完了
            'completed_at' => null, // 完了日時も空
        ];
    }

    /**
     * 完了済み状態のTodoを作る🎯
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed' => true,
            'completed_at' => now(),
        ]);
    }
}