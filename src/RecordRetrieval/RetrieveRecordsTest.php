<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordRetrieval;

use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use craft\db\Query;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use function assert;

/** @psalm-suppress PropertyNotSetInConstructor */
class RetrieveRecordsTest extends TestCase
{
    public function testRetrieveForScheduleTrackingRecord(): void
    {
        $querySpy2 = $this->createMock(
            Query::class,
        );

        $querySpy2->expects(self::once())
            ->method('all')
            ->willReturn([
                [
                    'id' => '123',
                    'class_name' => 'test-class-name',
                    'method' => 'test-method',
                    'run_every' => 'test-run-every',
                    'is_running' => '1',
                    'last_run_start_at' => '1982-01-27 00:00:00',
                    'last_run_end_at' => '1983-01-28 00:00:00',
                ],
                [
                    'id' => null,
                    'class_name' => 'test-class-name-2',
                    'method' => 'test-method-2',
                    'run_every' => 'test-run-every-2',
                    'is_running' => '0',
                    'last_run_start_at' => '1984-01-27 00:00:00',
                    'last_run_end_at' => '1971-01-28 00:00:00',
                ],
            ]);

        $querySpy1 = $this->createMock(
            Query::class,
        );

        $querySpy1->expects(self::once())
            ->method('from')
            ->with(self::equalTo(
                ScheduleTrackingRecord::table()
            ))
            ->willReturn($querySpy2);

        $querySpy1->expects(self::never())
            ->method('all');

        $retriever = new RetrieveRecords();

        $collection = $retriever->retrieve(
            classString: ScheduleTrackingRecord::class,
            query: $querySpy1,
        );

        self::assertSame(2, $collection->count());

        $record1 = $collection->filter(
            callback: static fn (ScheduleTrackingRecord $r) => $r->id() === 123,
        )->first();

        self::assertInstanceOf(
            ScheduleTrackingRecord::class,
            $record1,
        );

        assert($record1 instanceof ScheduleTrackingRecord);

        self::assertSame(
            '{{%schedule_tracking}}',
            $record1::table(),
        );

        self::assertSame(
            '{{%schedule_tracking}}',
            $record1->tableName(),
        );

        self::assertTrue($record1->isExisting());

        self::assertSame(
            [
                'id' => '123',
                'class_name' => 'test-class-name',
                'method' => 'test-method',
                'run_every' => 'test-run-every',
                'is_running' => '1',
                'last_run_start_at' => '1982-01-27 00:00:00',
                'last_run_end_at' => '1983-01-28 00:00:00',
            ],
            $record1->asArray(),
        );

        $this->runTestOnScheduleTrackingMethods($record1);

        $record2 = $collection->filter(
            callback: static fn (
                ScheduleTrackingRecord $r
            ) => $r->className() === 'test-class-name-2',
        )->first();

        self::assertInstanceOf(
            ScheduleTrackingRecord::class,
            $record1,
        );

        assert($record2 instanceof ScheduleTrackingRecord);

        self::assertSame(
            '{{%schedule_tracking}}',
            $record2::table(),
        );

        self::assertSame(
            '{{%schedule_tracking}}',
            $record2->tableName(),
        );

        self::assertFalse($record2->isExisting());

        self::assertSame(
            [
                'id' => null,
                'class_name' => 'test-class-name-2',
                'method' => 'test-method-2',
                'run_every' => 'test-run-every-2',
                'is_running' => '0',
                'last_run_start_at' => '1984-01-27 00:00:00',
                'last_run_end_at' => '1971-01-28 00:00:00',
            ],
            $record2->asArray(),
        );

        $this->runTestOnScheduleTrackingMethods($record2);
    }

    private function runTestOnScheduleTrackingMethods(
        ScheduleTrackingRecord $record
    ): void {
        $record->setId(id: null);
        self::assertNull($record->id());

        $record->setClassName(className: 'new-class-name');
        self::assertSame(
            'new-class-name',
            $record->className(),
        );

        $record->setMethod(method: 'new-method');
        self::assertSame(
            'new-method',
            $record->method(),
        );

        $record->setRunEvery(runEvery: 'new-run-every');
        self::assertSame(
            'new-run-every',
            $record->runEvery(),
        );

        $record->setIsRunning(isRunning: false);
        self::assertFalse($record->isRunning());

        $lastRunStartAtStub = new DateTimeImmutable();
        $record->setLastRunStartAt(lastRunStartAt: $lastRunStartAtStub);
        self::assertSame(
            $lastRunStartAtStub,
            $record->lastRunStartAt()
        );

        $lastRunEndAtStub = new DateTimeImmutable();
        $record->setLastRunEndAt(lastRunEndAt: $lastRunEndAtStub);
        self::assertSame(
            $lastRunEndAtStub,
            $record->lastRunEndAt()
        );
    }
}
