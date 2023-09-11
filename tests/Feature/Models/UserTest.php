<?php

namespace Tests\Feature\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase,ModelHelperTesting;

    protected function model(): Model
    {
        return new User();
    }

    public function testUserRelationshipWithPost(): void
    {
        $count = rand(1, 10);

        $user = User::factory()
//            ->has(Post::factory()->count($count))
            ->hasPosts($count)
            ->create();

        $this->assertCount($count,$user->posts);
        $this->assertTrue($user->posts->first() instanceof Post);
    }

    public function testUserRelationshipWithComment(): void
    {
        $count = rand(1, 10);

        $user = User::factory()
//            ->has(Post::factory()->count($count))
            ->hasComments($count)
            ->create();

        $this->assertCount($count,$user->comments);
        $this->assertTrue($user->comments->first() instanceof Comment);
    }
}
