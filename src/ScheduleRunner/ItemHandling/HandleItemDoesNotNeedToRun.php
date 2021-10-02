<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use BuzzingPixel\CraftScheduler\Output\OutputContract;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use yii\helpers\BaseConsole;

class HandleItemDoesNotNeedToRun implements ItemHandlerContract
{
    public function __construct(
        private CraftLogger $logger,
        private OutputContract $output,
        private ScheduleConfigItem $item,
    ) {
    }

    public function handle(): ItemHandlerResult
    {
        $message = $this->item->className() .
            ' does not need to run at this time';

        $this->logger->info(message: $message);

        $this->output->writeln(
            $message,
            BaseConsole::FG_GREEN,
        );

        return new ItemHandlerResult(ranSuccessfully: false);
    }
}
