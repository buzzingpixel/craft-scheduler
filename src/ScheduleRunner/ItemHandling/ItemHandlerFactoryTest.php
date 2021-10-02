<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

use BuzzingPixel\CraftScheduler\ContainerRetrieval\ContainerCollection;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\ContainerItem;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\RetrieveContainers;
use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRunner\MarkScheduleItemIsFinished;
use BuzzingPixel\CraftScheduler\ScheduleRunner\MarkScheduleItemIsRunning;
use BuzzingPixel\CraftScheduler\ScheduleRunner\ShouldRun;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/** @psalm-suppress PropertyNotSetInConstructor */
class ItemHandlerFactoryTest extends TestCase
{
    private function createLoggerStub(): CraftLogger
    {
        return $this->createMock(CraftLogger::class);
    }

    private function createShouldRunSpyWhenNotChecked(): ShouldRun
    {
        $mock = $this->createMock(ShouldRun::class);

        $mock->expects(self::never())
            ->method('check');

        return $mock;
    }

    private function createShouldRunSpyWhenShouldNotRun(
        ScheduleConfigItem $item,
    ): ShouldRun {
        $mock = $this->createMock(ShouldRun::class);

        $mock->expects(self::once())
            ->method('check')
            ->with(self::equalTo($item))
            ->willReturn(false);

        return $mock;
    }

    private function createShouldRunSpyWhenShouldRun(
        ScheduleConfigItem $item,
    ): ShouldRun {
        $mock = $this->createMock(ShouldRun::class);

        $mock->expects(self::once())
            ->method('check')
            ->with(self::equalTo($item))
            ->willReturn(true);

        return $mock;
    }

    private function createRetrieveContainersSpy(
        bool $hasClass
    ): RetrieveContainers {
        $container = $this->createMock(
            ContainerInterface::class,
        );

        $container->expects(self::once())
            ->method('has')
            ->with(self::equalTo('testClassName'))
            ->willReturn($hasClass);

        $containerItem = new ContainerItem(
            key: 'testResolveWith',
            container: $container,
        );

        $collection = new ContainerCollection(items: [$containerItem]);

        $mock = $this->createMock(
            RetrieveContainers::class,
        );

        $mock->expects(self::once())
            ->method('retrieve')
            ->willReturn($collection);

        return $mock;
    }

    private function createMarkScheduleItemIsRunningStub(): MarkScheduleItemIsRunning
    {
        return $this->createMock(
            MarkScheduleItemIsRunning::class,
        );
    }

    private function createMarkScheduleItemIsFinishedStub(): MarkScheduleItemIsFinished
    {
        return $this->createMock(
            MarkScheduleItemIsFinished::class,
        );
    }

    private function createScheduleConfigItem(
        bool $isRunning
    ): ScheduleConfigItem {
        return new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
            resolveWith: 'testResolveWith',
            isRunning: $isRunning,
        );
    }

    private function createOutputStub(): OutputContract
    {
        return $this->createMock(OutputContract::class);
    }

    public function testMakeWhenContainerDoesNotHaveClass(): void
    {
        $factory = new ItemHandlerFactory(
            logger: $this->createLoggerStub(),
            shouldRun: $this->createShouldRunSpyWhenNotChecked(),
            retrieveContainers: $this->createRetrieveContainersSpy(
                hasClass: false,
            ),
            markScheduleItemIsRunning: $this->createMarkScheduleItemIsRunningStub(),
            markScheduleItemIsFinished: $this->createMarkScheduleItemIsFinishedStub(),
        );

        self::assertInstanceOf(
            HandleContainerHasNoClass::class,
            $factory->make(
                item: $this->createScheduleConfigItem(isRunning: false),
                output: $this->createOutputStub(),
            ),
        );
    }

    public function testMakeWhenIsRunningAndShouldNotRun(): void
    {
        $item = $this->createScheduleConfigItem(isRunning: true);

        $factory = new ItemHandlerFactory(
            logger: $this->createLoggerStub(),
            shouldRun: $this->createShouldRunSpyWhenShouldNotRun(item: $item),
            retrieveContainers: $this->createRetrieveContainersSpy(
                hasClass: true,
            ),
            markScheduleItemIsRunning: $this->createMarkScheduleItemIsRunningStub(),
            markScheduleItemIsFinished: $this->createMarkScheduleItemIsFinishedStub(),
        );

        self::assertInstanceOf(
            HandleItemIsRunning::class,
            $factory->make(
                item: $item,
                output: $this->createOutputStub(),
            ),
        );
    }

    public function testMakeWhenShouldNotRun(): void
    {
        $item = $this->createScheduleConfigItem(isRunning: false);

        $factory = new ItemHandlerFactory(
            logger: $this->createLoggerStub(),
            shouldRun: $this->createShouldRunSpyWhenShouldNotRun(item: $item),
            retrieveContainers: $this->createRetrieveContainersSpy(
                hasClass: true,
            ),
            markScheduleItemIsRunning: $this->createMarkScheduleItemIsRunningStub(),
            markScheduleItemIsFinished: $this->createMarkScheduleItemIsFinishedStub(),
        );

        self::assertInstanceOf(
            HandleItemDoesNotNeedToRun::class,
            $factory->make(
                item: $item,
                output: $this->createOutputStub(),
            ),
        );
    }

    public function testMakeWhenShouldRun(): void
    {
        $item = $this->createScheduleConfigItem(isRunning: false);

        $factory = new ItemHandlerFactory(
            logger: $this->createLoggerStub(),
            shouldRun: $this->createShouldRunSpyWhenShouldRun(item: $item),
            retrieveContainers: $this->createRetrieveContainersSpy(
                hasClass: true,
            ),
            markScheduleItemIsRunning: $this->createMarkScheduleItemIsRunningStub(),
            markScheduleItemIsFinished: $this->createMarkScheduleItemIsFinishedStub(),
        );

        self::assertInstanceOf(
            HandleItem::class,
            $factory->make(
                item: $item,
                output: $this->createOutputStub(),
            ),
        );
    }
}
