<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class ScheduleConfigItemCollectionTest extends TestCase
{
    public function testFromItems(): void
    {
        $itemStub = new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
        );

        $collection = ScheduleConfigItemCollection::fromItems(items: [$itemStub]);

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $itemStub,
            $collection->map(static fn (
                ScheduleConfigItem $i,
            ) => $i)[0],
        );
    }
}
