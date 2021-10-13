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
     * @phpstan-ignore-next-line
     * @psalm-suppress MissingParamType
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

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     * @psalm-suppress MissingParamType
     */
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
     * @phpstan-ignore-next-line
     * @psalm-suppress MissingParamType
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

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     * @psalm-suppress MissingParamType
     */
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
