<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner;

use BuzzingPixel\CraftScheduler\Clock\ClockInterface;
use BuzzingPixel\CraftScheduler\RecordPersistence\PersistRecord;
use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;

class MarkScheduleItemIsFinished
{
    public function __construct(
        private ClockInterface $clock,
        private PersistRecord $persistRecord,
    ) {
    }

    public function mark(ScheduleConfigItem $item): void
    {
        $item->setIsRunning(isRunning: false);

        $item->setLastRunEndAt(dateTime: $this->clock->now());

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
    }
}
