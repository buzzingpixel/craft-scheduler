<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Clock;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class FrozenClockTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testNow(): void
    {
        $dateTimeStub = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC'),
        );

        $frozenClock = new FrozenClock($dateTimeStub);

        self::assertSame(
            $dateTimeStub,
            $frozenClock->now(),
        );
    }
}
