<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRunner\ItemHandling;

interface ItemHandlerContract
{
    public function handle(): ItemHandlerResult;
}
