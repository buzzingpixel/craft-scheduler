<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use yii\base\Event;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class RetrieveScheduleEvent extends Event
{
    private ScheduleConfigItemCollection $scheduleConfigItems;

    public function __construct()
    {
        parent::__construct();

        $this->scheduleConfigItems = new ScheduleConfigItemCollection();
    }

    public function scheduleConfigItems(): ScheduleConfigItemCollection
    {
        return $this->scheduleConfigItems;
    }
}
