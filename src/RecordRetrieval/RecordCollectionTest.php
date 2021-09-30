<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordRetrieval;

use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use DateTimeImmutable;
use LogicException;
use PHPUnit\Framework\TestCase;
use Throwable;

use function assert;

/** @psalm-suppress PropertyNotSetInConstructor */
class RecordCollectionTest extends TestCase
{
    public function testMap(): void
    {
        $dateStub = new DateTimeImmutable();

        $record1Stub = new ScheduleTrackingRecord(
            id: 123,
            className: 'test-class-1',
            method: 'test-method-1',
            runEvery: 'test-run-every-1',
            isRunning: true,
            lastRunStartAt: $dateStub,
            lastRunEndAt: $dateStub,
        );

        $record2Stub = new ScheduleTrackingRecord(
            id: 456,
            className: 'test-class-2',
            method: 'test-method-2',
            runEvery: 'test-run-every-2',
            isRunning: false,
            lastRunStartAt: $dateStub,
            lastRunEndAt: $dateStub,
        );

        $instance = new RecordCollection([
            $record1Stub,
            $record2Stub,
        ]);

        self::assertSame(
            [
                'test-class-1',
                'test-class-2',
            ],
            $instance->map(static fn (
                ScheduleTrackingRecord $r
            ) => $r->className()),
        );
    }

    public function testFirstWhenNoRecords(): void
    {
        $instance = new RecordCollection();

        $exception = null;

        try {
            $instance->first();
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof LogicException);

        self::assertSame(
            'Collection has no records',
            $exception->getMessage(),
        );
    }

    public function testFirstOrNullWhenNoRecords(): void
    {
        $instance = new RecordCollection();

        self::assertNull(
            $instance->firstOrNull(),
        );
    }

    public function testFirstOrNullWhenHasRecords(): void
    {
        $dateStub = new DateTimeImmutable();

        $record1Stub = new ScheduleTrackingRecord(
            id: 123,
            className: 'test-class-1',
            method: 'test-method-1',
            runEvery: 'test-run-every-1',
            isRunning: true,
            lastRunStartAt: $dateStub,
            lastRunEndAt: $dateStub,
        );

        $record2Stub = new ScheduleTrackingRecord(
            id: 456,
            className: 'test-class-2',
            method: 'test-method-2',
            runEvery: 'test-run-every-2',
            isRunning: false,
            lastRunStartAt: $dateStub,
            lastRunEndAt: $dateStub,
        );

        $instance = new RecordCollection([
            $record1Stub,
            $record2Stub,
        ]);

        self::assertSame(
            $record1Stub,
            $instance->firstOrNull(),
        );
    }
}
