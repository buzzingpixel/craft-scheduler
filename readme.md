# Scheduler for Craft CMS

If you would like to programmatically schedule tasks to run in Craft CMS, this is the module for you! Just set up a cron to call the command every minute, then anything you schedule to run by hooking into the event will get run on the schedule you define.

## Here's how to use it:

1. In your craft project, run `composer require buzzingpixel/craft-scheduler`
2. Then run `php craft plugin/install craft-scheduler`
3. Now hook into the `RetrieveSchedule` event to add items to the schedule

### The `RetrieveSchedule` event

The `RetrieveSchedule` event is how you add runners to the schedule. Here's a demo:

```php
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\RetrieveSchedule;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\RetrieveScheduleEvent;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\ScheduleConfigItem;
use BuzzingPixel\CraftScheduler\Frequency;
use yii\base\Event;

Event::on(
    RetrieveSchedule::class,
    RetrieveSchedule::EVENT_RETRIEVE_SCHEDULE,
    static function (RetrieveScheduleEvent $e): void {
        $e->scheduleConfigItems()->addItem(item: new ScheduleConfigItem(
            className: SomeClass::class, // The class to run
            runEvery: Frequency::ALWAYS, // How often to run it
            method: 'myOptionalMethod', // Specify method on the class to call, defaults to __invoke
            resolveWith: SomeContainer::class, // Optionally provide your own ContainerInterface implementation. Defaults to the Yii container (or whatever default container you specify, see below)
        ));

        $e->scheduleConfigItems()->addItem(item: new ScheduleConfigItem(
            className: SomeOtherClass::class,
            runEvery: Frequency::HOUR,
        ));
    }
);
```

### The `SetDefaultContainer` event

The scheduler allows you to provide your own ContainerInterface implementation as the default. If no default is specified, Craft Scheduler's Yii Container implementation (a wrapper around the Yii container that implements the PSR ContainerInterface) will be used.

```php
use BuzzingPixel\CraftScheduler\CraftSchedulerPlugin;
use BuzzingPixel\CraftScheduler\ScheduleRetrieval\SetDefaultContainerEvent;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use yii\base\Event;

use function DI\autowire;

Event::on(
    CraftSchedulerPlugin::class,
    CraftSchedulerPlugin::EVEN_SET_DEFAULT_CONTAINER,
    static function (SetDefaultContainerEvent $e) {
        $containerBuilder = (new ContainerBuilder())
            ->useAnnotations(true)
            ->useAutowiring(true)
            ->ignorePhpDocErrors(true)
            ->addDefinitions([
                ResponseFactoryInterface::class => autowire(ResponseFactory::class),
            ]);
        
        $container = $containerBuilder->build();

        $e->setDefaultContainer($container);
    }
);
```
