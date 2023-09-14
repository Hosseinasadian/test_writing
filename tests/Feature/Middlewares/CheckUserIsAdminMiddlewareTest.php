<?php

namespace Tests\Feature\Middlewares;

use App\Http\Middleware\CheckUserIsAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;


class CheckUserIsAdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function testWhenUserIsNotAdmin(): void
    {
/*        $user = User::factory()->count(2)->state(new Sequence(
            ['type'=>'user'],
            ['type'=>'admin'],
        ))->create();*/

        $user = User::factory()->user()->create();

        $this->actingAs($user);

        $request = Request::create('/admin','GET');

        $middleware = new CheckUserIsAdmin();

        $response = $middleware->handle($request,function (){

        });

        $this->assertEquals($response->getStatusCode(),302);
    }

    public function testWhenUserIsAdmin(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user);

        $request = Request::create('/admin','GET');

        $middleware = new CheckUserIsAdmin();

        $response = $middleware->handle($request,function (){
            return new Response();
        });

        $this->assertEquals($response,new Response());
    }

    public function testWhenUserNotLoggedIn(): void
    {
        $request = Request::create('/admin','GET');

        $middleware = new CheckUserIsAdmin();

        $response = $middleware->handle($request,function (){

        });

        $this->assertEquals($response->getStatusCode(),302);
    }

}
