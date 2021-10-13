<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Containers;

use Psr\Container\ContainerInterface;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

/**
 * @codeCoverageIgnore
 */
class YiiContainer implements ContainerInterface
{
    /** @var mixed[] */
    private array $instances = [];

    /**
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     *
     * @psalm-suppress MixedInferredReturnType
     */
    public function get(string $id): object
    {
        /**
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedArrayAccess
         */
        $instance = $this->instances[$id] ?? null;

        if ($instance === null) {
            /**
             * @psalm-suppress UndefinedClass
             * @phpstan-ignore-next-line
             */
            $instance = Yii::$container->get($id);

            /** @psalm-suppress MixedAssignment */
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    public function has(string $id): bool
    {
        try {
            $this->get($id);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
