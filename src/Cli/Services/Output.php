<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Cli\Services;

use BuzzingPixel\CraftScheduler\Output\OutputContract;
use yii\console\Controller;

use function array_merge;
use function call_user_func_array;

use const PHP_EOL;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint

/**
 * @codeCoverageIgnore
 */
class Output implements OutputContract
{
    public function __construct(
        private Controller $yiiConsoleController
    ) {
    }

    /**
     * @inheritDoc
     */
    public function write(string $message, ...$decorations): void
    {
        $args = array_merge([$message], $decorations);

        call_user_func_array(
            [
                $this->yiiConsoleController,
                'stdout',
            ],
            $args,
        );
    }

    public function writeln(string $message, ...$decorations): void
    {
        $args = array_merge([$message . PHP_EOL], $decorations);

        call_user_func_array(
            [
                $this->yiiConsoleController,
                'stdout',
            ],
            $args,
        );
    }

    /**
     * @inheritDoc
     */
    public function writeErr(string $message, ...$decorations): void
    {
        $args = array_merge([$message], $decorations);

        call_user_func_array(
            [
                $this->yiiConsoleController,
                'stderr',
            ],
            $args,
        );
    }

    public function writelnErr(string $message, ...$decorations): void
    {
        $args = array_merge([$message . PHP_EOL], $decorations);

        call_user_func_array(
            [
                $this->yiiConsoleController,
                'stderr',
            ],
            $args,
        );
    }
}
