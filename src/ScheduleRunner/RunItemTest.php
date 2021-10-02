<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner;

use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling\ItemHandlerContract;
use BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling\ItemHandlerFactory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class RunItemTest extends TestCase
{
    public function testRun(): void
    {
        $itemStub = new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
            method: 'testMethod',
            resolveWith: 'testResolveWith',
            persistentId: 123,
            isRunning: true,
            lastRunStartAt: new DateTimeImmutable(),
            lastRunEndAt: new DateTimeImmutable(),
        );

        $outputContractStub = $this->createMock(
            OutputContract::class,
        );

        $itemHandlerSpy = $this->createMock(
            ItemHandlerContract::class,
        );

        $itemHandlerSpy->expects(self::once())
            ->method('handle');

        $itemHandlerFactorySpy = $this->createMock(
            ItemHandlerFactory::class,
        );

        $itemHandlerFactorySpy->expects(self::once())
            ->method('make')
            ->with(
                self::equalTo($itemStub),
                self::equalTo($outputContractStub),
            )
            ->willReturn($itemHandlerSpy);

        $instance = new RunItem(
            itemHandlerFactory: $itemHandlerFactorySpy,
        );

        $instance->run(item: $itemStub, output: $outputContractStub);
    }
}
