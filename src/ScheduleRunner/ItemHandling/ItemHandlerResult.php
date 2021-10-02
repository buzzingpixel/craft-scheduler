<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

class ItemHandlerResult
{
    public function __construct(
        private bool $ranSuccessfully,
    ) {
    }

    public function ranSuccessfully(): bool
    {
        return $this->ranSuccessfully;
    }
}
