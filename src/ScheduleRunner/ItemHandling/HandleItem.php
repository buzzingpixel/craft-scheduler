<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRunner\MarkScheduleItemIsFinished;
use BuzzingPixel\CraftScheduler\ScheduleRunner\MarkScheduleItemIsRunning;
use Psr\Container\ContainerInterface;
use Throwable;
use yii\helpers\BaseConsole;

class HandleItem implements ItemHandlerContract
{
    public function __construct(
        private CraftLogger $logger,
        private OutputContract $output,
        private ScheduleConfigItem $item,
        private ContainerInterface $container,
        private MarkScheduleItemIsRunning $markScheduleItemIsRunning,
        private MarkScheduleItemIsFinished $markScheduleItemIsFinished,
    ) {
    }

    public function handle(): ItemHandlerResult
    {
        try {
            $message = 'Running schedule item: ' .
                $this->item->className();

            $this->logger->info(message: $message);

            $this->output->writeln(
                $message,
                BaseConsole::FG_GREEN,
            );

            $this->markScheduleItemIsRunning->mark(item: $this->item);

            /** @psalm-suppress MixedAssignment */
            $classInstance = $this->container->get(
                $this->item->className()
            );

            /**
             * @psalm-suppress MixedMethodCall
             * @phpstan-ignore-next-line
             */
            $classInstance->{$this->item->method()}();

            $this->markScheduleItemIsFinished->mark(item: $this->item);

            $message = $this->item->className() . ' ran successfully';

            $this->logger->info(message: $message);

            $this->output->writeln(
                $message,
                BaseConsole::FG_GREEN,
            );

            return new ItemHandlerResult(ranSuccessfully: true);
        } catch (Throwable $e) {
            $this->markScheduleItemIsFinished->mark(item: $this->item);

            $message = 'An exception was thrown while running: ' .
                $this->item->className() . ' Message: ' . $e->getMessage();

            $this->logger->error(message: $message);

            $this->output->writelnErr(
                $message,
                BaseConsole::FG_RED,
            );

            return new ItemHandlerResult(ranSuccessfully: false);
        }
    }
}
