<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use BuzzingPixel\CraftScheduler\RecordRetrieval\RetrieveRecords;
use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use craft\db\Query;

use function assert;

class MergeConfigItemsWithRecords
{
    public function __construct(private RetrieveRecords $retrieveRecords)
    {
    }

    public function merge(
        ScheduleConfigItemCollection $configItems
    ): ScheduleConfigItemCollection {
        $records = $this->retrieveRecords->retrieve(
            ScheduleTrackingRecord::class,
            new Query()
        );

        /** @psalm-suppress MixedArgument */
        return new ScheduleConfigItemCollection($configItems->map(
            static function (
                ScheduleConfigItem $configItem
            ) use ($records): ScheduleConfigItem {
                $record = $records->filter(
                    static function (
                        ScheduleTrackingRecord $record
                    ) use (
                        $configItem
                    ): bool {
                        $configClassName = $configItem->className();

                        return $record->className() === $configClassName &&
                            $record->method() === $configItem->method() &&
                            $record->runEvery() === $configItem->runEvery();
                    }
                )->firstOrNull();

                assert(
                    $record instanceof ScheduleTrackingRecord ||
                    $record === null
                );

                return ScheduleConfigItem::fromItemAndRecord(
                    configItem: $configItem,
                    record: $record,
                );
            }
        ));
    }
}
