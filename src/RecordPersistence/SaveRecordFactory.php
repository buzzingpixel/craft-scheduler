<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordPersistence;

use BuzzingPixel\CraftScheduler\Records\RecordContract;

class SaveRecordFactory
{
    public function __construct(
        private SaveNewRecord $saveNewRecord,
        private SaveExistingRecord $saveExistingRecord,
    ) {
    }

    public function make(RecordContract $record): SaveRecordContract
    {
        if ($record->isExisting()) {
            return $this->saveExistingRecord;
        }

        return $this->saveNewRecord;
    }
}
