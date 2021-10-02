<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

use BuzzingPixel\CraftScheduler\ContainerRetrieval\RetrieveContainers;
use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRunner\MarkScheduleItemIsFinished;
use BuzzingPixel\CraftScheduler\ScheduleRunner\MarkScheduleItemIsRunning;
use BuzzingPixel\CraftScheduler\ScheduleRunner\ShouldRun;

class ItemHandlerFactory
{
    public function __construct(
        private CraftLogger $logger,
        private ShouldRun $shouldRun,
        private RetrieveContainers $retrieveContainers,
        private MarkScheduleItemIsRunning $markScheduleItemIsRunning,
        private MarkScheduleItemIsFinished $markScheduleItemIsFinished,
    ) {
    }

    public function make(
        ScheduleConfigItem $item,
        OutputContract $output,
    ): ItemHandlerContract {
        $container = $this->retrieveContainers->retrieve()->getContainerByKey(
            $item->resolveWith(),
        );

        if (! $container->has($item->className())) {
            return new HandleContainerHasNoClass(
                logger: $this->logger,
                output: $output,
                item: $item,
            );
        }

        $shouldRun = $this->shouldRun->check(item: $item);

        if ($item->isRunning() && ! $shouldRun) {
            return new HandleItemIsRunning(
                logger: $this->logger,
                output: $output,
                item: $item,
            );
        }

        if (! $shouldRun) {
            return new HandleItemDoesNotNeedToRun(
                logger: $this->logger,
                output: $output,
                item: $item,
            );
        }

        return new HandleItem(
            item: $item,
            output: $output,
            logger: $this->logger,
            container: $container,
            markScheduleItemIsRunning: $this->markScheduleItemIsRunning,
            markScheduleItemIsFinished: $this->markScheduleItemIsFinished,
        );
    }
}
