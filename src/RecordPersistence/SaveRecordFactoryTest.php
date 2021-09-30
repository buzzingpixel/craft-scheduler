<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordPersistence;

use BuzzingPixel\CraftScheduler\Records\RecordContract;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class SaveRecordFactoryTest extends TestCase
{
    public function testMakeWhenRecordIsExisting(): void
    {
        $recordStub = $this->createMock(
            RecordContract::class
        );

        $recordStub->method('isExisting')->willReturn(true);

        $saveNewRecordStub = $this->createMock(
            SaveNewRecord::class,
        );

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $factory = new SaveRecordFactory(
            saveNewRecord: $saveNewRecordStub,
            saveExistingRecord: $saveExistingRecord,
        );

        self::assertSame(
            $saveExistingRecord,
            $factory->make($recordStub),
        );
    }

    public function testMakeWhenRecordIsNotExisting(): void
    {
        $recordStub = $this->createMock(
            RecordContract::class
        );

        $recordStub->method('isExisting')->willReturn(false);

        $saveNewRecordStub = $this->createMock(
            SaveNewRecord::class,
        );

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class,
        );

        $factory = new SaveRecordFactory(
            saveNewRecord: $saveNewRecordStub,
            saveExistingRecord: $saveExistingRecord,
        );

        self::assertSame(
            $saveNewRecordStub,
            $factory->make($recordStub),
        );
    }
}
