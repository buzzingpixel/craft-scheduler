<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler;

use BuzzingPixel\CraftScheduler\Cli\Commands\ScheduleCommand;
use BuzzingPixel\CraftScheduler\Clock\ClockInterface;
use BuzzingPixel\CraftScheduler\Clock\SystemClock;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\ContainerItem;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\RetrieveContainers;
use BuzzingPixel\CraftScheduler\ContainerRetrieval\RetrieveContainersEvent;
use BuzzingPixel\CraftScheduler\Containers\CreateNewInstanceContainer;
use BuzzingPixel\CraftScheduler\Containers\YiiContainer;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\SetDefaultContainerEvent;
use Craft;
use craft\base\Plugin;
use craft\db\Connection;
use Yii;
use yii\base\Event;

/**
 * @codeCoverageIgnore
 * @psalm-suppress PropertyNotSetInConstructor
 */
class CraftSchedulerPlugin extends Plugin
{
    public const EVEN_SET_DEFAULT_CONTAINER = 'setDefaultContainer';

    public function init(): void
    {
        parent::init();

        $this->registerDependencies();

        $this->mapControllers();

        $this->setDefaultContainerFromEvent();

        $this->registerInternalContainers();
    }

    private function registerDependencies(): void
    {
        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        if (Yii::$container->has(Connection::class)) {
            return;
        }

        /**
         * @psalm-suppress UndefinedClass
         * @phpstan-ignore-next-line
         */
        Yii::$container->set(
            Connection::class,
            Craft::$app->getDb(),
        );

        Yii::$container->set(
            ClockInterface::class,
            SystemClock::class,
        );
    }

    private function mapControllers(): void
    {
        $this->controllerMap = [
            'schedule' => ScheduleCommand::class,
        ];
    }

    private function setDefaultContainerFromEvent(): void
    {
        $setDefault = new SetDefaultContainerEvent();

        $this->trigger(
            self::EVEN_SET_DEFAULT_CONTAINER,
            $setDefault,
        );

        if ($setDefault->defaultContainer() === null) {
            return;
        }

        ScheduleConfigItem::$defaultResolveWith = $setDefault->defaultContainer();
    }

    private function registerInternalContainers(): void
    {
        Event::on(
            RetrieveContainers::class,
            RetrieveContainers::EVENT_RETRIEVE_CONTAINERS,
            static function (RetrieveContainersEvent $e): void {
                $e->containerConfigItems()->addItem(new ContainerItem(
                    CreateNewInstanceContainer::class,
                    new CreateNewInstanceContainer(),
                ));

                $e->containerConfigItems()->addItem(new ContainerItem(
                    YiiContainer::class,
                    new YiiContainer(),
                ));
            }
        );
    }
}
