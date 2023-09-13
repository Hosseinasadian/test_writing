<?php

namespace Tests\Feature\Views;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SingleViewTest extends TestCase
{
    use RefreshDatabase;

    public function testSingleViewRenderedWhenUserLoggedIn(): void
    {
        $post = Post::factory()->create();
        $comments = [];

        $view = (string)$this
            ->actingAs(User::factory()->create())
            ->view('single',compact('post','comments'));

        $dom = new \DOMDocument();
        $dom->loadHTML($view);

        $action = route('single.comment',$post->id);

        $dom = new \DOMXPath($dom);
        $this->assertCount(
            1,
            $dom->query("//form[@method='post'][@action = '$action']//textarea[@name='text']")
        );
    }

    public function testSingleViewRenderedWhenUserNotLoggedIn(): void
    {
        $post = Post::factory()->create();
        $comments = [];

        $view = (string)$this
            ->view('single',compact('post','comments'));

        $dom = new \DOMDocument();
        $dom->loadHTML($view);

        $action = route('single.comment',$post->id);

        $dom = new \DOMXPath($dom);
        $this->assertCount(
            0,
            $dom->query("//form[@method='post'][@action = '$action']//textarea[@name='text']")
        );
    }

}
