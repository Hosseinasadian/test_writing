<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testIndexMethod(): void
    {
        Post::factory()->count(100)->create();

        $this->actingAs(User::factory()->admin()->create())->get(route('post.index'))
            ->assertOk()
            ->assertViewIs('admin.post.index')
            ->assertViewHas(
                'posts',
                Post::query()->latest()->paginate()
            );

        $this->assertEquals(request()->route()->middleware(),[
            'web','admin'
        ]);
    }

    public function testCreateMethod(): void
    {
        Tag::factory()->count(20)->create();

        $this->actingAs(User::factory()->admin()->create())->get(route('post.create'))
            ->assertOk()
            ->assertViewIs('admin.post.create')
            ->assertViewHas(
                'tags',
                Tag::query()->latest()->get()
            );

        $this->assertEquals(request()->route()->middleware(),[
            'web','admin'
        ]);
    }

    public function testEditMethod(): void
    {
        $post = Post::factory()->create();
        Tag::factory()->count(20)->create();

        $this->actingAs(User::factory()->admin()->create())->get(route('post.edit', $post->id))
            ->assertOk()
            ->assertViewIs('admin.post.edit')
            ->assertViewHasAll([
                'post' => $post,
                'tags' => Tag::query()->latest()->get()
            ]);

        $this->assertEquals(request()->route()->middleware(),[
            'web','admin'
        ]);
    }

}
