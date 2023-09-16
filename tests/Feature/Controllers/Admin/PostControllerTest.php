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

    protected $middlewares = [
        'web', 'admin'
    ];

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

        $this->assertEquals(request()->route()->middleware(), $this->middlewares);
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

        $this->assertEquals(request()->route()->middleware(), $this->middlewares);
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

        $this->assertEquals(request()->route()->middleware(), $this->middlewares);
    }

    public function testStoreMethod()
    {
        $user = User::factory()->admin()->create();
        $data = Post::factory()->state([
            'user_id' => $user->id
        ])->make()->toArray();
        $tags = Tag::factory()->count(rand(1, 5))->create();

        $this->actingAs($user)
            ->post(route('post.store'), array_merge(
                $data, [
                    'tags' => $tags->pluck('id')->toArray()
                ]
            ))
            ->assertSessionHas('message', 'new post has been created')
            ->assertRedirect(route('post.index'));

        $this->assertDatabaseHas('posts', $data);
        $this->assertEquals(
            $tags->pluck('id')->toArray(),
            Post::query()->where($data)->first()->tags->pluck('id')->toArray()
        );
        $this->assertEquals(request()->route()->middleware(), $this->middlewares);

    }

    public function testUpdateMethod()
    {
        $user = User::factory()->admin()->create();
        $data = Post::factory()->state([
            'user_id' => $user->id
        ])->make()->toArray();
        $tags = Tag::factory()->count(rand(1, 5))->create();

        $post = Post::factory()
            ->state([
                'user_id' => $user->id
            ])
            ->hasTags(rand(1, 5))
            ->create();

        $this->actingAs($user)
            ->patch(route('post.update', $post->id), array_merge(
                $data, [
                    'tags' => $tags->pluck('id')->toArray()
                ]
            ))
            ->assertSessionHas('message', 'the post has been updated')
            ->assertRedirect(route('post.index'));

        $this->assertDatabaseHas('posts', array_merge($data, [
            'id' => $post->id
        ]));
        $this->assertEquals(
            $tags->pluck('id')->toArray(),
            Post::query()->where($data)->first()->tags->pluck('id')->toArray()
        );
        $this->assertEquals(request()->route()->middleware(), $this->middlewares);

    }

    public function testValidationRequestRequiredData()
    {
        $user = User::factory()->admin()->create();
        $errors = [
            'title' => 'The title field is required.',
            'description' => 'The description field is required.',
            'image' => 'The image field is required.',
            'tags' => 'The tags field is required.',
        ];
        $data = [];

        //store method
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->patch(route('post.update', $post->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationRequestDescriptionDataHasMinimumRule()
    {
        $user = User::factory()->admin()->create();
        $errors = [
            'description' => 'The description field must be at least 5 characters.',
        ];
        $data = [
            'description' => 'Php'
        ];

        //store method
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->patch(route('post.update', $post->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationRequestImageDataHasUrlRule()
    {
        $user = User::factory()->admin()->create();
        $errors = [
            'image' => 'The image field must be a valid URL.',
        ];
        $data = [
            'image' => 'Php'
        ];

        //store method
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->patch(route('post.update', $post->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationRequestTagsDataHasArrayRule()
    {
        $user = User::factory()->admin()->create();
        $errors = [
            'tags' => 'The tags field must be an array.',
        ];
        $data = [
            'tags' => 'Php'
        ];

        //store method
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->patch(route('post.update', $post->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationRequestTagsDataMustExistsInTagsTable()
    {
        $user = User::factory()->admin()->create();
        $errors = [
            'tags.0' => 'The selected tags.0 is invalid.',
        ];
        $data = [
            'tags' => [0]
        ];

        //store method
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        //update method
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->patch(route('post.update', $post->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testDestroyMethod()
    {
        $post = Post::factory()
            ->hasTags(5)
            ->hasComments(1)
            ->create();

        $comment = $post->comments()->first();
        $tags = $post->tags;

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('post.destroy', $post->id))
            ->assertSessionHasAll(['message' => 'the post has been deleted'])
            ->assertRedirect(route('post.index'));

        $this->assertModelMissing($post)
            ->assertModelMissing($comment);

        foreach ($tags as $tag) {
            $this->assertDatabaseMissing('post_tag', [
                'post_id' => $post->id,
                'tag_id' => $tag->id,
            ]);
        }

        $this->assertEquals(request()->route()->middleware(), $this->middlewares);

    }

}
