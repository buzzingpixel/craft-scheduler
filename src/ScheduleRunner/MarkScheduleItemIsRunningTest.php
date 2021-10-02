<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner;

use BuzzingPixel\CraftScheduler\Clock\DateFormats;
use BuzzingPixel\CraftScheduler\Clock\FrozenClock;
use BuzzingPixel\CraftScheduler\RecordPersistence\PersistRecord;
use BuzzingPixel\CraftScheduler\RecordRetrieval\RecordCollection;
use BuzzingPixel\CraftScheduler\RecordRetrieval\RetrieveRecords;
use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use yii\db\Query;

use function assert;

/** @psalm-suppress PropertyNotSetInConstructor */
class MarkScheduleItemIsRunningTest extends TestCase
{
    private ?ScheduleTrackingRecord $persistedRecord = null;

    private ?string $retrieveRecordsClassString = null;

    private ?Query $retrieveRecordsQuery = null;

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
            persistentId: 789,
            isRunning: false,
            lastRunStartAt: $lastRunStartAt,
            lastRunEndAt: $lastRunEndAt,
        );

        $dbRecordStub = new ScheduleTrackingRecord(
            id: 456,
            className: 'testClassName',
            method: 'testMethod',
            runEvery: 'testRunEvery',
            isRunning: true,
            lastRunEndAt: $lastRunEndAt,
            lastRunStartAt: $lastRunStartAt,
        );

        $retrieveRecordsStub = $this->createMock(
            RetrieveRecords::class
        );

        $retrieveRecordsStub->method('retrieve')
            ->willReturnCallback(function (
                string $classString,
                Query $query,
            ) use ($dbRecordStub): RecordCollection {
                $this->retrieveRecordsClassString = $classString;

                $this->retrieveRecordsQuery = $query;

                return new RecordCollection(records: [$dbRecordStub]);
            });

        $instance = new MarkScheduleItemIsRunning(
            clock: new FrozenClock(time: $persistentDateTimeStub),
            persistRecord: $persistRecordStub,
            retrieveRecords: $retrieveRecordsStub,
        );

        $instance->mark($itemStub);

        self::assertTrue($itemStub->isRunning());

        self::assertSame(
            $persistentDateTimeStub,
            $itemStub->lastRunStartAt(),
        );

        self::assertInstanceOf(
            ScheduleTrackingRecord::class,
            $this->persistedRecord,
        );

        assert(
            $this->persistedRecord instanceof ScheduleTrackingRecord
        );

        self::assertSame(
            789,
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

        self::assertTrue($this->persistedRecord->isRunning());

        self::assertSame(
            $persistentDateTimeStub,
            $this->persistedRecord->lastRunStartAt(),
        );

        self::assertSame(
            $lastRunEndAt,
            $this->persistedRecord->lastRunEndAt(),
        );
    }
}
