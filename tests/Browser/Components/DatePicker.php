<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class DatePicker extends BaseComponent
{
    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return '.dp-cal.dp-flyout';
    }

    /**
     * Assert that the browser page contains the component.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertVisible($this->selector());
    }

    public function selectDate(Browser $browser, $year, $month, $day)
    {
        $browser
            ->click('button.dp-cal-year')
            ->with('.dp-years.dp-submenu', function ($list) use ($year) {
                $list->press($year);
            })
            ->click('button.dp-cal-month')
            ->with('.dp-months.dp-submenu', function ($list) use ($month) {
                $list->press($month);
            })
            ->with('.dp-days', function ($list) use ($day) {
                $list->script("
                        $('button.dp-day:not(.dp-edge-day):contains($day)')
                            .first().click();
                    ");
            });
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }
}
