<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordPersistence;

use BuzzingPixel\CraftScheduler\Records\RecordContract;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class PersistRecordTest extends TestCase
{
    public function testPersist(): void
    {
        $recordStub = $this->createMock(
            RecordContract::class
        );

        $saveRecordSpy = $this->createMock(
            SaveRecordContract::class,
        );

        $saveRecordSpy->expects(self::once())
            ->method('save')
            ->with(self::equalTo($recordStub));

        $saveRecordFactorySpy = $this->createMock(
            SaveRecordFactory::class,
        );

        $saveRecordFactorySpy->expects(self::once())
            ->method('make')
            ->with(self::equalTo($recordStub))
            ->willReturn($saveRecordSpy);

        $instance = new PersistRecord(saveRecordFactory: $saveRecordFactorySpy);

        $instance->persist(record: $recordStub);
    }
}
