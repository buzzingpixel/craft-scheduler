<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use BuzzingPixel\CraftScheduler\RecordRetrieval\RecordCollection;
use BuzzingPixel\CraftScheduler\RecordRetrieval\RetrieveRecords;
use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use craft\db\Query;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class MergeConfigItemsWithRecordsTest extends TestCase
{
    public function testMerge(): void
    {
        $recordStub1LastRunStartAt = new DateTimeImmutable();
        $recordStub1LastRunEndAt   = new DateTimeImmutable();
        $recordStub1               = new ScheduleTrackingRecord(
            id: 123,
            className: 'testClassName1',
            method: 'testMethod1',
            runEvery: 'testRunEvery1',
            isRunning: true,
            lastRunStartAt: $recordStub1LastRunStartAt,
            lastRunEndAt: $recordStub1LastRunEndAt,
        );

        $recordStub2LastRunStartAt = new DateTimeImmutable();
        $recordStub2LastRunEndAt   = new DateTimeImmutable();
        $recordStub2               = new ScheduleTrackingRecord(
            id: 456,
            className: 'testClassName2',
            method: 'testMethod2',
            runEvery: 'testRunEvery2',
            isRunning: false,
            lastRunStartAt: $recordStub2LastRunStartAt,
            lastRunEndAt: $recordStub2LastRunEndAt,
        );

        $retrieveRecordsSpy = $this->createMock(
            RetrieveRecords::class,
        );

        $retrieveRecordsSpy->expects(self::once())
            ->method('retrieve')
            ->with(
                self::equalTo(
                    ScheduleTrackingRecord::class,
                ),
                self::isInstanceOf(Query::class),
            )
            ->willReturn(new RecordCollection([
                $recordStub2,
                $recordStub1,
            ]));

        $inputItem1 = new ScheduleConfigItem(
            className: 'testClassName1',
            runEvery: 'testRunEvery1',
            method: 'testMethod1',
            resolveWith: 'testResolveWith1',
        );

        $inputItem2 = new ScheduleConfigItem(
            className: 'testClassName2',
            runEvery: 'testRunEvery2',
            method: 'testMethod2',
            resolveWith: 'testResolveWith2',
        );

        $inputCollection = new ScheduleConfigItemCollection(items: [
            $inputItem1,
            $inputItem2,
        ]);

        $service = new MergeConfigItemsWithRecords(
            retrieveRecords: $retrieveRecordsSpy,
        );

        $returnCollection = $service->merge(configItems: $inputCollection);

        self::assertSame(2, $returnCollection->count());

        /** @var ScheduleConfigItem[] $returnItems */
        $returnItems = $returnCollection->map(
            static fn (ScheduleConfigItem $i) => $i,
        );

        $returnItem1 = $returnItems[0];

        self::assertSame(
            'testClassName1',
            $returnItem1->className(),
        );

        self::assertSame(
            'testRunEvery1',
            $returnItem1->runEvery(),
        );

        self::assertSame(
            'testMethod1',
            $returnItem1->method(),
        );

        self::assertSame(
            'testResolveWith1',
            $returnItem1->resolveWith(),
        );

        self::assertSame(
            123,
            $returnItem1->persistentId(),
        );

        self::assertTrue($returnItem1->isRunning());

        self::assertSame(
            $recordStub1LastRunStartAt,
            $returnItem1->lastRunStartAt(),
        );

        self::assertSame(
            $recordStub1LastRunEndAt,
            $returnItem1->lastRunEndAt(),
        );

        $returnItem2 = $returnItems[1];

        self::assertSame(
            'testClassName2',
            $returnItem2->className(),
        );

        self::assertSame(
            'testRunEvery2',
            $returnItem2->runEvery(),
        );

        self::assertSame(
            'testMethod2',
            $returnItem2->method(),
        );

        self::assertSame(
            'testResolveWith2',
            $returnItem2->resolveWith(),
        );

        self::assertSame(
            456,
            $returnItem2->persistentId(),
        );

        self::assertFalse($returnItem2->isRunning());

        self::assertSame(
            $recordStub2LastRunStartAt,
            $returnItem2->lastRunStartAt(),
        );

        self::assertSame(
            $recordStub2LastRunEndAt,
            $returnItem2->lastRunEndAt(),
        );
    }
}
