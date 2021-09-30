<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Records;

use BuzzingPixel\CraftScheduler\Clock\DateFormats;
use DateTimeImmutable;
use DateTimeZone;

use function assert;
use function is_string;

class ScheduleTrackingRecord implements RecordContract
{
    public static function table(): string
    {
        return '{{%schedule_tracking}}';
    }

    public function tableName(): string
    {
        return self::table();
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $item): self
    {
        /** @psalm-suppress MixedAssignment */
        $id = $item['id'];

        if ($id !== null) {
            $id = (int) $id;
        }

        $lastRunStartAt = $item['last_run_start_at'];

        assert(
            $lastRunStartAt === null ||
            is_string($lastRunStartAt)
        );

        if ($lastRunStartAt !== null) {
            $lastRunStartAt = DateTimeImmutable::createFromFormat(
                DateFormats::MYSQL_STORAGE_FORMAT,
                $lastRunStartAt,
                new DateTimeZone('UTC'),
            );

            assert($lastRunStartAt instanceof DateTimeImmutable);
        }

        $lastRunEndAt = $item['last_run_end_at'];

        assert(
            $lastRunEndAt === null ||
            is_string($lastRunEndAt)
        );

        if ($lastRunEndAt !== null) {
            $lastRunEndAt = DateTimeImmutable::createFromFormat(
                DateFormats::MYSQL_STORAGE_FORMAT,
                $lastRunEndAt,
                new DateTimeZone('UTC'),
            );

            assert($lastRunEndAt instanceof DateTimeImmutable);
        }

        return new self(
            id: $id,
            className: (string) $item['class_name'],
            method: (string) $item['method'],
            runEvery: (string) $item['run_every'],
            isRunning: (bool) $item['is_running'],
            lastRunStartAt: $lastRunStartAt,
            lastRunEndAt: $lastRunEndAt,
        );
    }

    public function __construct(
        private ?int $id,
        private string $className,
        private string $method,
        private string $runEvery,
        private bool $isRunning,
        private ?DateTimeImmutable $lastRunStartAt,
        private ?DateTimeImmutable $lastRunEndAt,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function asArray(): array
    {
        return [
            'id' => $this->id() === null ? null : (string) $this->id(),
            'class_name' => $this->className(),
            'method' => $this->method(),
            'run_every' => $this->runEvery(),
            'is_running' => $this->isRunning() ? '1' : '0',
            'last_run_start_at' => $this->lastRunStartAt()?->format(
                DateFormats::MYSQL_STORAGE_FORMAT,
            ),
            'last_run_end_at' => $this->lastRunEndAt()?->format(
                DateFormats::MYSQL_STORAGE_FORMAT,
            ),
        ];
    }

    public function isExisting(): bool
    {
        return $this->id() !== null;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function runEvery(): string
    {
        return $this->runEvery;
    }

    public function setRunEvery(string $runEvery): self
    {
        $this->runEvery = $runEvery;

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

    public function setLastRunStartAt(?DateTimeImmutable $lastRunStartAt): self
    {
        $this->lastRunStartAt = $lastRunStartAt;

        return $this;
    }

    public function lastRunEndAt(): ?DateTimeImmutable
    {
        return $this->lastRunEndAt;
    }

    public function setLastRunEndAt(?DateTimeImmutable $lastRunEndAt): self
    {
        $this->lastRunEndAt = $lastRunEndAt;

        return $this;
    }
}
