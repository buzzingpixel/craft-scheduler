<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ContainerRetrieval;

use yii\base\Event;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class RetrieveContainersEvent extends Event
{
    private ContainerCollection $containerConfigItems;

    public function __construct()
    {
        parent::__construct();

        $this->containerConfigItems = new ContainerCollection();
    }

    public function containerConfigItems(): ContainerCollection
    {
        return $this->containerConfigItems;
    }
}
