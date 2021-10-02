<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRunner\MarkScheduleItemIsFinished;
use BuzzingPixel\CraftScheduler\ScheduleRunner\MarkScheduleItemIsRunning;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use yii\helpers\BaseConsole;

/** @psalm-suppress PropertyNotSetInConstructor */
class HandleItemTest extends TestCase
{
    /** @var mixed[] */
    private array $logs = [];

    /** @var mixed[] */
    private array $outputs = [];

    private string $containerCalledClass = '';

    /** @var mixed[] */
    private array $markCalls = [];

    public bool $ranSuccessfully = false;

    public function setUp(): void
    {
        $this->logs                 = [];
        $this->outputs              = [];
        $this->containerCalledClass = '';
        $this->ranSuccessfully      = false;
    }

    private function createLoggerStub(): CraftLogger
    {
        $loggerStub = $this->createMock(CraftLogger::class);

        $loggerStub->method('info')
            ->willReturnCallback(
                fn (string $msg) => $this->logs[] = [
                    'method' => 'info',
                    'msg' => $msg,
                ]
            );

        $loggerStub->method('error')
            ->willReturnCallback(
                fn (string $msg) => $this->logs[] = [
                    'method' => 'error',
                    'msg' => $msg,
                ]
            );

        return $loggerStub;
    }

    private function createOutputStub(): OutputContract
    {
        $outputStub = $this->createMock(
            OutputContract::class
        );

        $outputStub->method('writeln')
            ->willReturnCallback(fn (
                string $msg,
                int $decorations,
            ) => $this->outputs[] = [
                'method' => 'writeln',
                'msg' => $msg,
                'decorations' => $decorations,
            ]);

        $outputStub->method('writelnErr')
            ->willReturnCallback(fn (
                string $msg,
                int $decorations,
            ) => $this->outputs[] = [
                'method' => 'writelnErr',
                'msg' => $msg,
                'decorations' => $decorations,
            ]);

        return $outputStub;
    }

    private function createContainerStubThrowable(): ContainerInterface
    {
        $containerStub = $this->createMock(
            ContainerInterface::class,
        );

        $containerStub->method('get')
            ->willReturnCallback(
                function (string $className): object {
                    $this->containerCalledClass = $className;

                    return new class () {
                        public function testClassMethod(): void
                        {
                            throw new Exception('testExceptionMsg');
                        }
                    };
                }
            );

        return $containerStub;
    }

    private function createContainerStubSuccess(): ContainerInterface
    {
        $containerStub = $this->createMock(
            ContainerInterface::class,
        );

        $containerStub->method('get')
            ->willReturnCallback(
                function (string $className): object {
                    $this->containerCalledClass = $className;

                    $that = $this;

                    return new class ($that) {
                        public function __construct(
                            private HandleItemTest $that
                        ) {
                        }

                        public function testClassMethod(): void
                        {
                            $this->that->ranSuccessfully = true;
                        }
                    };
                }
            );

        return $containerStub;
    }

    private function createMarkScheduleItemIsRunningStub(): MarkScheduleItemIsRunning
    {
        $stub = $this->createMock(
            MarkScheduleItemIsRunning::class,
        );

        $stub->method('mark')
            ->willReturnCallback(fn (
                ScheduleConfigItem $item
            ) => $this->markCalls[] = [
                'called' => 'markScheduleItemIsRunning',
                'item' => $item,
            ]);

        return $stub;
    }

    private function createMarkScheduleItemIsFinishedStub(): MarkScheduleItemIsFinished
    {
        $stub = $this->createMock(
            MarkScheduleItemIsFinished::class,
        );

        $stub->method('mark')
            ->willReturnCallback(fn (
                ScheduleConfigItem $item
            ) => $this->markCalls[] = [
                'called' => 'markScheduleItemIsFinished',
                'item' => $item,
            ]);

        return $stub;
    }

    public function testHandleWhenClassInstanceThrowsException(): void
    {
        $itemStub = new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
            method: 'testClassMethod',
        );

        $startMessage = 'Running schedule item: ' . $itemStub->className();

        $exceptionMessage = 'An exception was thrown while running: ' .
            $itemStub->className() .
            ' Message: testExceptionMsg';

        $instance = new HandleItem(
            logger: $this->createLoggerStub(),
            output: $this->createOutputStub(),
            item: $itemStub,
            container: $this->createContainerStubThrowable(),
            markScheduleItemIsRunning: $this->createMarkScheduleItemIsRunningStub(),
            markScheduleItemIsFinished: $this->createMarkScheduleItemIsFinishedStub(),
        );

        self::assertFalse($instance->handle()->ranSuccessfully());

        self::assertCount(2, $this->logs);

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'info',
            $this->logs[0]['method'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $startMessage,
            $this->logs[0]['msg'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'error',
            $this->logs[1]['method'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $exceptionMessage,
            $this->logs[1]['msg'],
        );

        self::assertCount(2, $this->outputs);

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'writeln',
            $this->outputs[0]['method'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'Running schedule item: testClassName',
            $this->outputs[0]['msg'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            BaseConsole::FG_GREEN,
            $this->outputs[0]['decorations'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'writelnErr',
            $this->outputs[1]['method'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $exceptionMessage,
            $this->outputs[1]['msg'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            BaseConsole::FG_RED,
            $this->outputs[1]['decorations'],
        );

        self::assertSame(
            'testClassName',
            $this->containerCalledClass,
        );

        self::assertCount(2, $this->markCalls);

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'markScheduleItemIsRunning',
            $this->markCalls[0]['called'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $itemStub,
            $this->markCalls[0]['item'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'markScheduleItemIsFinished',
            $this->markCalls[1]['called'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $itemStub,
            $this->markCalls[1]['item'],
        );
    }

    public function testHandleWhenClassRunsSuccessfully(): void
    {
        $itemStub = new ScheduleConfigItem(
            className: 'testClassName',
            runEvery: 'testRunEvery',
            method: 'testClassMethod',
        );

        $startMessage = 'Running schedule item: ' . $itemStub->className();

        $finishedMessage = $itemStub->className() . ' ran successfully';

        $instance = new HandleItem(
            logger: $this->createLoggerStub(),
            output: $this->createOutputStub(),
            item: $itemStub,
            container: $this->createContainerStubSuccess(),
            markScheduleItemIsRunning: $this->createMarkScheduleItemIsRunningStub(),
            markScheduleItemIsFinished: $this->createMarkScheduleItemIsFinishedStub(),
        );

        self::assertTrue($instance->handle()->ranSuccessfully());

        self::assertCount(2, $this->logs);

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'info',
            $this->logs[0]['method'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $startMessage,
            $this->logs[0]['msg'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'info',
            $this->logs[1]['method'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $finishedMessage,
            $this->logs[1]['msg'],
        );

        self::assertCount(2, $this->outputs);

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'writeln',
            $this->outputs[0]['method'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'Running schedule item: testClassName',
            $this->outputs[0]['msg'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            BaseConsole::FG_GREEN,
            $this->outputs[0]['decorations'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'writeln',
            $this->outputs[1]['method'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $finishedMessage,
            $this->outputs[1]['msg'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            BaseConsole::FG_GREEN,
            $this->outputs[1]['decorations'],
        );

        self::assertSame(
            'testClassName',
            $this->containerCalledClass,
        );

        self::assertCount(2, $this->markCalls);

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'markScheduleItemIsRunning',
            $this->markCalls[0]['called'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $itemStub,
            $this->markCalls[0]['item'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            'markScheduleItemIsFinished',
            $this->markCalls[1]['called'],
        );

        /** @psalm-suppress MixedArrayAccess */
        self::assertSame(
            $itemStub,
            $this->markCalls[1]['item'],
        );
    }
}
