<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordRetrieval;

use BuzzingPixel\CraftScheduler\Records\RecordContract;
use LogicException;
use Throwable;

use function array_filter;
use function array_map;
use function array_values;
use function count;

class RecordCollection
{
    /** @var RecordContract[] */
    private array $records = [];

    /**
     * @param RecordContract[] $records
     */
    public function __construct(array $records = [])
    {
        array_map(
            [$this, 'addItem'],
            $records,
        );
    }

    private function addItem(RecordContract $item): void
    {
        $this->records[] = $item;
    }

    public function map(callable $callback): mixed
    {
        return array_map(
            $callback,
            $this->records,
        );
    }

    public function filter(callable $callback): RecordCollection
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return new self(array_filter(
            $this->records,
            $callback,
        ));
    }

    public function first(): RecordContract
    {
        if (count($this->records) < 1) {
            throw new LogicException('Collection has no records');
        }

        return array_values($this->records)[0];
    }

    public function firstOrNull(): ?RecordContract
    {
        try {
            return $this->first();
        } catch (Throwable) {
            return null;
        }
    }

    public function count(): int
    {
        return count($this->records);
    }
}
