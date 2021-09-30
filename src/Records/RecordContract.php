<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Records;

interface RecordContract
{
    public static function table(): string;

    public function tableName(): string;

    /**
     * @param mixed[] $item
     */
    public static function fromArray(array $item): self;

    /**
     * @return mixed[]
     */
    public function asArray(): array;

    public function isExisting(): bool;

    public function id(): ?int;
}
