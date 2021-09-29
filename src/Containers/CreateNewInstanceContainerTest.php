<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Containers;

use BuzzingPixel\CraftScheduler\Frequency;
use BuzzingPixel\CraftScheduler\ScheduleRunner\ShouldRun;
use Error;
use PHPUnit\Framework\TestCase;
use Throwable;

use function assert;

/** @psalm-suppress PropertyNotSetInConstructor */
class CreateNewInstanceContainerTest extends TestCase
{
    public function testGetWhenClassDoesNotExist(): void
    {
        $container = new CreateNewInstanceContainer();

        $exception = null;

        try {
            $container->get('\foo\bar');
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof Error);

        self::assertInstanceOf(
            Error::class,
            $exception,
        );

        self::assertSame(
            'Class "\foo\bar" not found',
            $exception->getMessage(),
        );
    }

    public function testGetWhenClassExists(): void
    {
        $container = new CreateNewInstanceContainer();

        $instance1 = $container->get(Frequency::class);

        $instance2 = $container->get(Frequency::class);

        self::assertInstanceOf(Frequency::class, $instance1);

        self::assertSame($instance1, $instance2);
    }

    public function testHasWhenClassDoesNotExist(): void
    {
        $container = new CreateNewInstanceContainer();

        self::assertFalse($container->has('\foo\bar'));
    }

    public function testHasWhenNotInstantiable(): void
    {
        $container = new CreateNewInstanceContainer();

        self::assertFalse($container->has(
            ShouldRun::class,
        ));
    }

    public function testHasWhenHasInstantiableClass(): void
    {
        $container = new CreateNewInstanceContainer();

        self::assertTrue($container->has(
            Frequency::class,
        ));
    }
}
