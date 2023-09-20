<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class CreatePostPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/admin/post/create';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url())
            ->assertInputPresent('title')
            ->assertInputPresent('description')
            ->assertInputPresent('tags')
            ->assertAttribute('select[name="tags"]', 'multiple', 'true')
            ->assertPresent('@postImageInput')
            ->assertInputPresent('image');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@element' => '#selector',
            '@postImageInput' => 'input[type="file"]#postImageInput',
            '@submitButton' => 'form button[type="submit"]'
        ];
    }
}
