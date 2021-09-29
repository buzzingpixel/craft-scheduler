<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Clock;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class SystemClockTest extends TestCase
{
    public function testNow(): void
    {
        $dateTimeStub = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC'),
        );

        $systemClock = new SystemClock();

        self::assertSame(
            $dateTimeStub->format(DateTimeInterface::ATOM),
            $systemClock->now()->format(DateTimeInterface::ATOM),
        );
    }
}
