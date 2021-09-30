<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ContainerRetrieval;

use Psr\Container\ContainerInterface;

class ContainerItem
{
    public function __construct(
        private string $key,
        private ContainerInterface $container,
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
