<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use PHPUnit\Framework\TestCase;
use yii\base\Event;

/** @psalm-suppress PropertyNotSetInConstructor */
class RetrieveScheduleTest extends TestCase
{
    public function testRetrieve(): void
    {
        $configItemStub1 = new ScheduleConfigItem(
            'test-class-name',
            'test-frequency',
        );

        $configItemStub2 = new ScheduleConfigItem(
            'test-class-name',
            'test-frequency',
        );

        Event::on(
            RetrieveSchedule::class,
            RetrieveSchedule::EVENT_RETRIEVE_SCHEDULE,
            static function (RetrieveScheduleEvent $e) use (
                $configItemStub1,
                $configItemStub2,
            ): void {
                $e->scheduleConfigItems()->addItem(
                    item: $configItemStub1,
                );

                $e->scheduleConfigItems()->addItem(
                    item: $configItemStub2,
                );
            }
        );

        $scheduleConfigStub = new ScheduleConfigItemCollection();

        $mergeConfigItemsWithRecordsSpy = $this->createMock(
            MergeConfigItemsWithRecords::class,
        );

        $mergeConfigItemsWithRecordsSpy->expects(self::once())
            ->method('merge')
            ->willReturnCallback(
                static function (
                    ScheduleConfigItemCollection $collection,
                ) use (
                    $scheduleConfigStub,
                    $configItemStub1,
                    $configItemStub2,
                ): ScheduleConfigItemCollection {
                    self::assertSame(
                        2,
                        $collection->count()
                    );

                    self::assertFalse($collection->isEmpty());

                    /** @psalm-suppress MixedAssignment */
                    $array = $collection->map(
                        static fn (ScheduleConfigItem $i) => $i,
                    );

                    /** @psalm-suppress MixedArrayAccess */
                    self::assertSame(
                        $configItemStub1,
                        $array[0],
                    );

                    /** @psalm-suppress MixedArrayAccess */
                    self::assertSame(
                        $configItemStub2,
                        $array[1],
                    );

                    return $scheduleConfigStub;
                }
            );

        $instance = new RetrieveSchedule(
            mergeConfigItemsWithRecords: $mergeConfigItemsWithRecordsSpy,
        );

        self::assertSame(
            $scheduleConfigStub,
            $instance->retrieve(),
        );
    }
}
