<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner;

use BuzzingPixel\CraftScheduler\Clock\ClockInterface;
use BuzzingPixel\CraftScheduler\RecordPersistence\PersistRecord;
use BuzzingPixel\CraftScheduler\RecordRetrieval\RetrieveRecords;
use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use craft\db\Query;

use function assert;

class MarkScheduleItemIsRunning
{
    public function __construct(
        private ClockInterface $clock,
        private PersistRecord $persistRecord,
        private RetrieveRecords $retrieveRecords,
    ) {
    }

    public function mark(ScheduleConfigItem $item): void
    {
        $item->setIsRunning(isRunning: true);

        $item->setLastRunStartAt(dateTime: $this->clock->now());

        $record = new ScheduleTrackingRecord(
            id: $item->persistentId(),
            className: $item->className(),
            method: $item->method(),
            runEvery: (string) $item->runEvery(),
            isRunning: $item->isRunning(),
            lastRunStartAt: $item->lastRunStartAt(),
            lastRunEndAt: $item->lastRunEndAt(),
        );

        $this->persistRecord->persist($record);

        $dbRecord = $this->retrieveRecords->retrieve(
            classString: ScheduleTrackingRecord::class,
            query: (new Query())
                ->where('`class_name` = :className', [
                    'className' => $item->className(),
                ])
                ->andWhere('`method` = :method', [
                    'method' => $item->method(),
                ])
                ->andWhere('`run_every` = :runEvery', [
                    'runEvery' => $item->runEvery(),
                ])
        )->first();

        assert($dbRecord instanceof ScheduleTrackingRecord);

        $item->setPersistentId((int) $dbRecord->id());
    }
}
