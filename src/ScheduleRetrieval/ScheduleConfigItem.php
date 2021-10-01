<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\ScheduleRetrieval;

use BuzzingPixel\CraftScheduler\Containers\YiiContainer;
use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use DateTimeImmutable;

class ScheduleConfigItem
{
    public static function fromItemAndRecord(
        ScheduleConfigItem $configItem,
        ?ScheduleTrackingRecord $record
    ): self {
        return new self(
            className: $configItem->className(),
            runEvery: $configItem->runEvery(),
            method: $configItem->method(),
            resolveWith: $configItem->resolveWith(),
            persistentId: $record?->id(),
            isRunning: $record !== null && $record->isRunning(),
            lastRunStartAt: $record?->lastRunStartAt(),
            lastRunEndAt: $record?->lastRunEndAt()
        );
    }

    public static string $defaultResolveWith = YiiContainer::class;

    private string $resolveWith;

    public function __construct(
        private string $className,
        private float | int | string $runEvery,
        private string $method = '__invoke',
        ?string $resolveWith = null,
        private ?int $persistentId = null,
        private bool $isRunning = false,
        private ?DateTimeImmutable $lastRunStartAt = null,
        private ?DateTimeImmutable $lastRunEndAt = null,
    ) {
        if ($resolveWith !== null) {
            $this->resolveWith = $resolveWith;

            return;
        }

        $this->resolveWith = self::$defaultResolveWith;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function runEvery(): float|int|string
    {
        return $this->runEvery;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function resolveWith(): string
    {
        return $this->resolveWith;
    }

    public function persistentId(): ?int
    {
        return $this->persistentId;
    }

    public function setPersistentId(int $id): self
    {
        $this->persistentId = $id;

        return $this;
    }

    public function isRunning(): bool
    {
        return $this->isRunning;
    }

    public function setIsRunning(bool $isRunning): self
    {
        $this->isRunning = $isRunning;

        return $this;
    }

    public function lastRunStartAt(): ?DateTimeImmutable
    {
        return $this->lastRunStartAt;
    }

    public function setLastRunStartAt(DateTimeImmutable $dateTime): self
    {
        $this->lastRunStartAt = $dateTime;

        return $this;
    }

    public function lastRunEndAt(): ?DateTimeImmutable
    {
        return $this->lastRunEndAt;
    }

    public function setLastRunEndAt(DateTimeImmutable $dateTime): self
    {
        $this->lastRunEndAt = $dateTime;

        return $this;
    }
}
