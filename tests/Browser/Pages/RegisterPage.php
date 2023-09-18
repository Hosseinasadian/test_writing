<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class RegisterPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/register';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@submitButton' => 'form button[type="submit"]',
        ];
    }

    public function submitForm(Browser $browser, array $data = [])
    {
        $browser
            ->type('name', $data['name'] ?? '')
            ->type('email', $data['email'] ?? '')
            ->type('password', $data['password'] ?? '')
            ->type('password_confirmation', $data['password'] ?? '')
            ->click('@submitButton');
    }
}
