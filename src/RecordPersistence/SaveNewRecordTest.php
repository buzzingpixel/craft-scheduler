<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordPersistence;

use BuzzingPixel\CraftScheduler\Records\RecordContract;
use craft\db\Command;
use craft\db\Connection;
use PHPUnit\Framework\TestCase;
use stdClass;
use yii\db\Exception;

/** @psalm-suppress PropertyNotSetInConstructor */
class SaveNewRecordTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSave(): void
    {
        $dataStore = new stdClass();

        $dataStore->insert = false;

        $recordStub = $this->createMock(
            RecordContract::class
        );

        $recordStub->method('tableName')
            ->willReturn('test-table');

        $recordStub->method('asArray')
            ->willReturn(['test-data']);

        $commandSpy = $this->createMock(
            Command::class,
        );

        $commandSpy->expects(self::once())
            ->method('insert')
            ->willReturnCallback(
                static function (
                    string $table,
                    array $recordData,
                ) use (
                    $dataStore,
                    $commandSpy,
                ): Command {
                    $dataStore->updateWasCalled = true;

                    self::assertSame(
                        'test-table',
                        $table,
                    );

                    self::assertSame(
                        ['test-data'],
                        $recordData,
                    );

                    return $commandSpy;
                }
            );

        $commandSpy->expects(self::once())
            ->method('execute')
            ->willReturnCallback(static function () use (
                $dataStore
            ): int {
                self::assertTrue($dataStore->updateWasCalled);

                return 1;
            });

        $connectionStub = $this->createMock(
            Connection::class
        );

        $connectionStub->method('createCommand')
            ->willReturn($commandSpy);

        $instance = new SaveNewRecord(dbConnection: $connectionStub);

        $instance->save(record: $recordStub);
    }
}
