<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ContainerRetrieval;

use Psr\Container\ContainerInterface;

use function array_filter;
use function array_map;
use function array_values;
use function count;

class ContainerCollection
{
    /** @var ContainerItem[] */
    private array $items = [];

    /**
     * @param ContainerItem[] $items
     */
    public function __construct(array $items = [])
    {
        array_map(
            [$this, 'addItem'],
            $items,
        );
    }

    public function addItem(ContainerItem $item): void
    {
        $this->items[] = $item;
    }

    public function getContainerByKey(string $key): ContainerInterface
    {
        $filtered = array_filter(
            $this->items,
            static fn (ContainerItem $i) => $i->key() === $key,
        );

        return array_values($filtered)[0]->getContainer();
    }

    public function count(): int
    {
        return count($this->items);
    }
}
