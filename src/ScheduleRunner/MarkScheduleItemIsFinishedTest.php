<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner;

use BuzzingPixel\CraftScheduler\Clock\DateFormats;
use BuzzingPixel\CraftScheduler\Clock\FrozenClock;
use BuzzingPixel\CraftScheduler\RecordPersistence\PersistRecord;
use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

use function assert;

/** @psalm-suppress PropertyNotSetInConstructor */
class MarkScheduleItemIsFinishedTest extends TestCase
{
    private ?ScheduleTrackingRecord $persistedRecord = null;

    public function testMark(): void
    {
        $persistentDateTimeStub = DateTimeImmutable::createFromFormat(
            DateFormats::MYSQL_STORAGE_FORMAT,
            '1982-01-27 00:00:00',
            new DateTimeZone('UTC'),
        );

        assert($persistentDateTimeStub instanceof DateTimeImmutable);

        $lastRunStartAt = DateTimeImmutable::createFromFormat(
            DateFormats::MYSQL_STORAGE_FORMAT,
            '1972-01-27 00:00:00',
            new DateTimeZone('UTC'),
        );

        assert($lastRunStartAt instanceof DateTimeImmutable);

        $lastRunEndAt = DateTimeImmutable::createFromFormat(
            DateFormats::MYSQL_STORAGE_FORMAT,
            '1962-01-27 00:00:00',
            new DateTimeZone('UTC'),
        );

        assert($lastRunEndAt instanceof DateTimeImmutable);

        $persistRecordStub = $this->createMock(
            PersistRecord::class
        );

        $persistRecordStub->expects(self::once())
            ->method('persist')
            ->willReturnCallback(fn (
                ScheduleTrackingRecord $r
            ) => $this->persistedRecord = $r);

        $itemStub = new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
            method: 'testMethod',
            resolveWith: 'testResolveWith',
            persistentId: 123,
            isRunning: true,
            lastRunStartAt: $lastRunStartAt,
            lastRunEndAt: $lastRunEndAt,
        );

        $instance = new MarkScheduleItemIsFinished(
            clock: new FrozenClock(time: $persistentDateTimeStub),
            persistRecord: $persistRecordStub,
        );

        $instance->mark($itemStub);

        self::assertFalse($itemStub->isRunning());

        self::assertSame(
            $persistentDateTimeStub,
            $itemStub->lastRunEndAt(),
        );

        self::assertInstanceOf(
            ScheduleTrackingRecord::class,
            $this->persistedRecord,
        );

        assert(
            $this->persistedRecord instanceof ScheduleTrackingRecord
        );

        self::assertSame(
            123,
            $this->persistedRecord->id(),
        );

        self::assertSame(
            'testClassName',
            $this->persistedRecord->className(),
        );

        self::assertSame(
            'testMethod',
            $this->persistedRecord->method(),
        );

        self::assertSame(
            'testRunEvery',
            $this->persistedRecord->runEvery(),
        );

        self::assertFalse($this->persistedRecord->isRunning());

        self::assertSame(
            $lastRunStartAt,
            $this->persistedRecord->lastRunStartAt(),
        );

        self::assertSame(
            $persistentDateTimeStub,
            $this->persistedRecord->lastRunEndAt(),
        );
    }
}
