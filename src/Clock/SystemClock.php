<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Clock;

use DateTimeImmutable;
use DateTimeZone;

class SystemClock implements ClockInterface
{
    /** @noinspection PhpUnhandledExceptionInspection */
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC'),
        );
    }
}
