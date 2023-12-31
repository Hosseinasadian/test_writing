<?php

namespace App\Helpers;

class DurationOfReading
{
    private $timePerWord = 1;
    private $wordLength;
    private $duration;

    public function setText(string $text)
    {
        $this->wordLength = count(explode(' ', $text));
        $this->duration = $this->timePerWord * $this->wordLength;

        return $this;
    }

    public function getTimePerSecond()
    {
        return $this->duration;
    }

    public function getTimePerMinute()
    {
        return $this->duration / 60;
    }
}
