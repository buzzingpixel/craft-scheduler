<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Output;

/**
 * @codeCoverageIgnore
 */
class NoOpOutput implements OutputContract
{
    /**
     * @inheritDoc
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function write(string $message, ...$decorations): void
    {
    }

    /**
     * @inheritDoc
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function writeln(string $message, ...$decorations): void
    {
    }

    /**
     * @inheritDoc
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function writeErr(string $message, ...$decorations): void
    {
    }

    /**
     * @inheritDoc
     * @psalm-suppress MissingParamType
     * @phpstan-ignore-next-line
     */
    public function writelnErr(string $message, ...$decorations): void
    {
    }
}
