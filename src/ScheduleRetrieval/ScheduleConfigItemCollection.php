<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use function array_map;
use function count;

class ScheduleConfigItemCollection
{
    /**
     * @param ScheduleConfigItem[] $items
     */
    public static function fromItems(array $items = []): self
    {
        return new self(items: $items);
    }

    /** @var ScheduleConfigItem[] */
    private array $items = [];

    /**
     * @param ScheduleConfigItem[] $items
     */
    public function __construct(array $items = [])
    {
        array_map(
            [$this, 'addItem'],
            $items,
        );
    }

    public function addItem(ScheduleConfigItem $item): void
    {
        $this->items[] = $item;
    }

    public function map(callable $callback): mixed
    {
        return array_map(
            $callback,
            $this->items,
        );
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->count() < 1;
    }
}
