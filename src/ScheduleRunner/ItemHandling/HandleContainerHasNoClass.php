<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use yii\helpers\BaseConsole;

class HandleContainerHasNoClass implements ItemHandlerContract
{
    public function __construct(
        private CraftLogger $logger,
        private OutputContract $output,
        private ScheduleConfigItem $item,
    ) {
    }

    public function handle(): ItemHandlerResult
    {
        $message = 'Running ' . $this->item->className() . ' on schedule ' .
            $this->item->runEvery() . ' failed because the class could ' .
            'not be created';

        $this->logger->error(message: $message);

        $this->output->writelnErr(
            $message,
            BaseConsole::FG_RED,
        );

        return new ItemHandlerResult(ranSuccessfully: false);
    }
}
