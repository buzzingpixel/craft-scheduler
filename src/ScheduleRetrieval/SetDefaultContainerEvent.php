<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use yii\base\Event;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class SetDefaultContainerEvent extends Event
{
    private ?string $defaultContainer = null;

    public function defaultContainer(): ?string
    {
        return $this->defaultContainer;
    }

    public function setDefaultContainer(string $defaultContainer): void
    {
        $this->defaultContainer = $defaultContainer;
    }
}
