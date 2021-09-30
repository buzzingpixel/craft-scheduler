<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Logging;

use Craft;
use Psr\Log\LoggerInterface;

use function var_export;

/**
 * @codeCoverageIgnore
 */
class CraftLogger implements LoggerInterface
{
    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::error(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::error(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::error(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::error(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::warning(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::info(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::info(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::info(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = []): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Craft::info(
            $message . ' Context: ' . var_export(
                $context,
                true
            ),
            'craft-scheduler'
        );
    }
}
