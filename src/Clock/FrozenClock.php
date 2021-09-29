<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Clock;

use DateTimeImmutable;

class FrozenClock implements ClockInterface
{
    private DateTimeImmutable $time;

    public function __construct(DateTimeImmutable $time)
    {
        $this->time = $time;
    }

    public function now(): DateTimeImmutable
    {
        return $this->time;
    }
}
