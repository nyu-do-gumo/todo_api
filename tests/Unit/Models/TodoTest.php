<?php
// tests/Unit/Models/TodoTest.php
namespace Tests\Unit\Models;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $todo->user);
        $this->assertEquals($user->id, $todo->user->id);
    }

    #[Test]
    public function it_can_be_marked_as_completed()
    {
        $todo = Todo::factory()->create(['completed' => false]);

        $todo->toggleAsCompleted();

        $this->assertTrue($todo->completed);
        $this->assertNotNull($todo->completed_at);
    }

    #[Test]
    public function it_can_be_marked_as_incomplete()
    {
        $todo = Todo::factory()->create(['completed' => true]);

        $todo->toggleAsCompleted();

        $this->assertFalse($todo->completed);
        $this->assertNull($todo->completed_at);
    }

    #[Test]
    public function it_can_create_a_todo()
    {
        $user = User::factory()->create();
        $todoData = [
            'title' => 'Test Todo',
            'completed' => false,
            'user_id' => $user->id,
        ];

        $todo = Todo::create($todoData);

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'completed' => false,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_can_update_a_todo()
    {
        $todo = Todo::factory()->create(['title' => 'Old Title']);

        $todo->update(['title' => 'New Title']);

        $this->assertDatabaseHas('todos', [
            'title' => 'New Title',
        ]);
    }

    #[Test]
    public function it_can_delete_a_todo()
    {
        $todo = Todo::factory()->create();

        $todo->delete();

        $this->assertModelMissing($todo);
    }
}