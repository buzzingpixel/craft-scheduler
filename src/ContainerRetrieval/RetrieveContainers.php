<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ContainerRetrieval;

use yii\base\Component;

class RetrieveContainers extends Component
{
    public const EVENT_RETRIEVE_CONTAINERS = 'retrieveContainers';

    public function retrieve(): ContainerCollection
    {
        $event = new RetrieveContainersEvent();

        $event->sender = $this;

        $this->trigger(
            self::EVENT_RETRIEVE_CONTAINERS,
            $event,
        );

        return $event->containerConfigItems();
    }
}
