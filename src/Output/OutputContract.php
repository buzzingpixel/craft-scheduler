<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Output;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint

interface OutputContract
{
    /**
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function write(string $message, ...$decorations): void;

    /**
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function writeln(string $message, ...$decorations): void;

    /**
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function writeErr(string $message, ...$decorations): void;

    /**
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function writelnErr(string $message, ...$decorations): void;
}
