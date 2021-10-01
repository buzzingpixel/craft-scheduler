<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class ScheduleConfigItemTest extends TestCase
{
    public function testDefaultResolveWith(): void
    {
        $item = new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
        );

        self::assertSame(
            ScheduleConfigItem::$defaultResolveWith,
            $item->resolveWith(),
        );
    }

    public function testScheduleConfigItemWhenRecordIsNull(): void
    {
        $dateTimeStub = new DateTimeImmutable();

        $inputItem = new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
            method: 'testMethod',
            resolveWith: 'testResolveWith',
            persistentId: 456,
            isRunning: true,
            lastRunStartAt: $dateTimeStub,
            lastRunEndAt: $dateTimeStub,
        );

        $item = ScheduleConfigItem::fromItemAndRecord(
            $inputItem,
            null,
        );

        self::assertSame(
            'testClassName',
            $item->className(),
        );

        self::assertSame(
            'testRunEvery',
            $item->runEvery(),
        );

        self::assertSame(
            'testMethod',
            $item->method(),
        );

        self::assertSame(
            'testResolveWith',
            $item->resolveWith(),
        );

        self::assertNull($item->persistentId());

        self::assertFalse($item->isRunning());

        self::assertNull($item->lastRunStartAt());

        self::assertNull($item->lastRunEndAt());

        $item->setPersistentId(123);
        self::assertSame(123, $item->persistentId());

        $item->setIsRunning(true);
        self::assertTrue($item->isRunning());

        $lastRunStartAtDateTime = new DateTimeImmutable();
        $item->setLastRunStartAt($lastRunStartAtDateTime);
        self::assertSame(
            $lastRunStartAtDateTime,
            $item->lastRunStartAt(),
        );

        $lastRunEndAtDateTime = new DateTimeImmutable();
        $item->setLastRunEndAt($lastRunEndAtDateTime);
        self::assertSame(
            $lastRunEndAtDateTime,
            $item->lastRunEndAt(),
        );
    }

    public function testScheduleConfigItemWhenRecordIsNotNull(): void
    {
        $dateTimeStub = new DateTimeImmutable();

        $inputItem = new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
            method: 'testMethod',
            resolveWith: 'testResolveWith',
            persistentId: 456,
            isRunning: true,
            lastRunStartAt: $dateTimeStub,
            lastRunEndAt: $dateTimeStub,
        );

        $recordLasRunStartAt = new DateTimeImmutable();

        $recordLastRunEndAt = new DateTimeImmutable();

        $inputRecord = new ScheduleTrackingRecord(
            id: 678,
            className: 'asdf',
            method: 'qwerty',
            runEvery: 'zxcvb',
            isRunning: true,
            lastRunStartAt: $recordLasRunStartAt,
            lastRunEndAt: $recordLastRunEndAt,
        );

        $item = ScheduleConfigItem::fromItemAndRecord(
            $inputItem,
            $inputRecord,
        );

        self::assertSame(
            'testClassName',
            $item->className(),
        );

        self::assertSame(
            'testRunEvery',
            $item->runEvery(),
        );

        self::assertSame(
            'testMethod',
            $item->method(),
        );

        self::assertSame(
            'testResolveWith',
            $item->resolveWith(),
        );

        self::assertSame(678, $item->persistentId());

        self::assertTrue($item->isRunning());

        self::assertSame(
            $recordLasRunStartAt,
            $item->lastRunStartAt(),
        );

        self::assertSame(
            $recordLastRunEndAt,
            $item->lastRunEndAt()
        );
    }
}
