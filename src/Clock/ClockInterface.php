<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Clock;

use DateTimeImmutable;

// phpcs:disable SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming.SuperfluousSuffix

interface ClockInterface
{
    /**
     * Returns the current time as a DateTimeImmutable Object
     */
    public function now(): DateTimeImmutable;
}
