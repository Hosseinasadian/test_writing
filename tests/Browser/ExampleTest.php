<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function testBasicExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->myAlert('Hello')
                ->pause(2000)
                ->assertDialogOpened('Hello')
                ->acceptDialog()
                ->pause(2000);
        });
    }
}
