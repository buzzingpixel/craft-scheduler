<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class SetDefaultContainerEventTest extends TestCase
{
    public function test(): void
    {
        $event = new SetDefaultContainerEvent();

        self::assertNull($event->defaultContainer());

        $event->setDefaultContainer('testContainer');

        self::assertSame(
            'testContainer',
            $event->defaultContainer(),
        );
    }
}
