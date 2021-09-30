<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ContainerRetrieval;

use BuzzingPixel\CraftScheduler\Containers\CreateNewInstanceContainer;
use BuzzingPixel\CraftScheduler\Containers\YiiContainer;
use PHPUnit\Framework\TestCase;
use yii\base\Event;

/** @psalm-suppress PropertyNotSetInConstructor */
class RetrieveContainersTest extends TestCase
{
    public function testRetrieve(): void
    {
        $instance = new RetrieveContainers();

        $containerItem1 = new ContainerItem(
            CreateNewInstanceContainer::class,
            new CreateNewInstanceContainer(),
        );

        $containerItem2 = new ContainerItem(
            YiiContainer::class,
            new YiiContainer(),
        );

        Event::on(
            RetrieveContainers::class,
            RetrieveContainers::EVENT_RETRIEVE_CONTAINERS,
            static function (RetrieveContainersEvent $e) use (
                $containerItem1,
                $containerItem2
            ): void {
                $e->containerConfigItems()->addItem(
                    item:$containerItem1,
                );

                $e->containerConfigItems()->addItem(
                    item:$containerItem2,
                );
            },
        );

        $collection = $instance->retrieve();

        self::assertSame(2, $collection->count());

        self::assertSame(
            $containerItem1->getContainer(),
            $collection->getContainerByKey(
                CreateNewInstanceContainer::class
            ),
        );

        self::assertSame(
            $containerItem2->getContainer(),
            $collection->getContainerByKey(
                YiiContainer::class
            ),
        );
    }
}
