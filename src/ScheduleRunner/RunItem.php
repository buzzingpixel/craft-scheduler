<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner;

use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling\ItemHandlerFactory;

class RunItem
{
    public function __construct(
        private ItemHandlerFactory $itemHandlerFactory,
    ) {
    }

    public function run(
        ScheduleConfigItem $item,
        OutputContract $output,
    ): void {
        $this->itemHandlerFactory->make(
            item: $item,
            output: $output,
        )->handle();
    }
}
