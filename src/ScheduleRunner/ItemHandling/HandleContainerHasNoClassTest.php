<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use PHPUnit\Framework\TestCase;
use yii\helpers\BaseConsole;

/** @psalm-suppress PropertyNotSetInConstructor */
class HandleContainerHasNoClassTest extends TestCase
{
    public function testHandle(): void
    {
        $itemStub = new ScheduleConfigItem(
            className: 'testClassname',
            runEvery: 'testRunEvery',
        );

        $message = 'Running ' . $itemStub->className() . ' on schedule ' .
            $itemStub->runEvery() . ' failed because the class could ' .
            'not be created';

        $loggerSpy = $this->createMock(CraftLogger::class);

        $loggerSpy->expects(self::once())
            ->method('error')
            ->with(self::equalTo($message));

        $outputSpy = $this->createMock(
            OutputContract::class,
        );

        $outputSpy->expects(self::once())
            ->method('writelnErr')
            ->with(
                self::equalTo($message),
                self::equalTo(BaseConsole::FG_RED),
            );

        $instance = new HandleContainerHasNoClass(
            logger: $loggerSpy,
            output: $outputSpy,
            item: $itemStub,
        );

        self::assertFalse($instance->handle()->ranSuccessfully());
    }
}
