<?php

namespace Tests\Feature\Views;

use App\Models\User;
use Tests\TestCase;

class LayoutViewTest extends TestCase
{
    public function testLayoutViewRenderedWhenUserIsAdmin(): void
    {
        $user = User::factory()->state(['type'=>'admin'])->create();
        $this->actingAs($user);

        $view = $this->view('layouts.layout');

        $view->assertSee('<a href="/admin/dashboard">admin panel</a>',false);
    }

    public function testLayoutViewRenderedWhenUserIsNotAdmin(): void
    {
        $user = User::factory()->state(['type'=>'user'])->create();
        $this->actingAs($user);

        $view = $this->view('layouts.layout');

        $view->assertDontSee('<a href="/admin/dashboard">admin panel</a>',false);
    }
}
