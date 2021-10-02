<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use PHPUnit\Framework\TestCase;
use yii\helpers\BaseConsole;

/** @psalm-suppress PropertyNotSetInConstructor */
class HandleItemDoesNotNeedToRunTest extends TestCase
{
    public function testHandle(): void
    {
        $itemStub = new ScheduleConfigItem(
            className: 'testClassname',
            runEvery: 'testRunEvery',
        );

        $message = $itemStub->className() .
            ' does not need to run at this time';

        $loggerSpy = $this->createMock(CraftLogger::class);

        $loggerSpy->expects(self::once())
            ->method('info')
            ->with(self::equalTo($message));

        $outputSpy = $this->createMock(
            OutputContract::class,
        );

        $outputSpy->expects(self::once())
            ->method('writeln')
            ->with(
                self::equalTo($message),
                self::equalTo(BaseConsole::FG_GREEN),
            );

        $instance = new HandleItemDoesNotNeedToRun(
            logger: $loggerSpy,
            output: $outputSpy,
            item: $itemStub,
        );

        self::assertFalse($instance->handle()->ranSuccessfully());
    }
}
