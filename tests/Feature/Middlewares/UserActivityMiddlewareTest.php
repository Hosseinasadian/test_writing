<?php

namespace Tests\Feature\Middlewares;

use App\Http\Middleware\CheckUserIsAdmin;
use App\Http\Middleware\UserActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserActivityMiddlewareTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCanSetUserActivityInCacheWhenUserLoggedIn(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user);

        $request = Request::create('/', 'GET');

        $middleware = new UserActivity();

        $response = $middleware->handle($request, function () {
            return new Response();
        });

        $this->assertEquals($response, new Response());
        $this->assertEquals('online', Cache::get("user-{$user->id}-status"));
        $this->travel(11)->seconds();
        $this->assertNull(Cache::get("user-{$user->id}-status"));
    }

    public function testCanSetUserActivityInCacheWhenUserNotLoggedIn(): void
    {
        $request = Request::create('/', 'GET');

        $middleware = new UserActivity();

        $response = $middleware->handle($request, function () {
            return new Response();
        });

        $this->assertEquals($response, new Response());
    }

    public function testUserActivityMiddlewareSetInWebMiddlewareGroup()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk();

        $this->assertEquals('online', Cache::get("user-{$user->id}-status"));
        $this->assertEquals(['web'], request()->route()->middleware());

    }

}
