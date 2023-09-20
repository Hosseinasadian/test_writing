<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\DatePicker;
use Tests\DuskTestCase;

class HomeTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testHomePage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('input.tiny-date-picker')
                ->with(new DatePicker, function ($browser) {
                    $browser->selectDate(1999, 'September', 2);
                })
                ->pause(5000)
                ->assertInputValue('input.tiny-date-picker', '9/2/1999');;
        });
    }
}
