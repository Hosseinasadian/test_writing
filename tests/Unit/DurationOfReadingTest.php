<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\DurationOfReading;

class DurationOfReadingTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function testCanGetDurationOfReadingText(): void
    {
        // 1 second per word
        $text = "this is for test";

        $dor = new DurationOfReading();
        $dor->setText($text);

        $this->assertEquals(4,$dor->getTimePerSecond());
        $this->assertEquals(4/60,$dor->getTimePerMinute());
    }
}
