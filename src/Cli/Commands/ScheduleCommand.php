<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Cli\Commands;

use BuzzingPixel\CraftScheduler\Cli\Services\Output;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\RetrieveSchedule;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRunner\RunItem;
use yii\console\Controller;
use yii\helpers\BaseConsole;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint

/**
 * @codeCoverageIgnore
 */
class ScheduleCommand extends Controller
{
    /**
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function __construct(
        $id,
        $module,
        $config,
        private Output $output,
        private RunItem $runItem,
        private RetrieveSchedule $retrieveSchedule,
    ) {
        /** @psalm-suppress MixedArgument */
        parent::__construct($id, $module, $config);
    }

    public function actionRun(): void
    {
        $schedule = $this->retrieveSchedule->retrieve();

        if ($schedule->isEmpty()) {
            $this->output->writeln(
                'There are no scheduled items yet',
                BaseConsole::FG_YELLOW,
            );

            return;
        }

        $schedule->map(
            function (ScheduleConfigItem $item): void {
                $this->runItem->run(
                    item: $item,
                    output: $this->output,
                );
            }
        );
    }
}
