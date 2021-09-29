<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Containers;

use Psr\Container\ContainerInterface;
use Throwable;

use function class_exists;

class CreateNewInstanceContainer implements ContainerInterface
{
    /** @var mixed[] */
    private array $instances = [];

    public function get(string $id): mixed
    {
        /**
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedArrayAccess
         */
        $instance = $this->instances[$id] ?? null;

        if ($instance === null) {
            /** @psalm-suppress InvalidStringClass */
            $instance = new $id();

            /** @psalm-suppress MixedAssignment */
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    public function has(string $id): bool
    {
        if (! class_exists($id)) {
            return false;
        }

        try {
            $this->get($id);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
