<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordPersistence;

use BuzzingPixel\CraftScheduler\Records\RecordContract;

class PersistRecord
{
    public function __construct(
        private SaveRecordFactory $saveRecordFactory,
    ) {
    }

    public function persist(RecordContract $record): void
    {
        $this->saveRecordFactory->make(record: $record)->save(record: $record);
    }
}
