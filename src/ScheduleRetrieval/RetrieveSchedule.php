<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use yii\base\Component;

class RetrieveSchedule extends Component
{
    public const EVENT_RETRIEVE_SCHEDULE = 'retrieveSchedule';

    public function __construct(
        private MergeConfigItemsWithRecords $mergeConfigItemsWithRecords
    ) {
        parent::__construct();
    }

    public function retrieve(): ScheduleConfigItemCollection
    {
        $event = new RetrieveScheduleEvent();

        $event->sender = $this;

        $this->trigger(
            self::EVENT_RETRIEVE_SCHEDULE,
            $event,
        );

        return $this->mergeConfigItemsWithRecords->merge(
            $event->scheduleConfigItems()
        );
    }
}
