<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testUserCanRegister(): void
    {
        $data = User::factory()->user()->make([
            'email_verified_at'=>null
        ])->toArray();

        $password = '12345678';

        Event::fake();

        $response = $this->post(route('register'), array_merge($data, [
            'password' => $password,
            'password_confirmation' => $password,
        ]));
        $response->assertRedirect();
        $this->assertDatabaseHas('users',$data);
        Event::assertDispatched(Registered::class);

    }
}
