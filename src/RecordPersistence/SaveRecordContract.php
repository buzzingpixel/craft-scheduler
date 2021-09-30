<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordPersistence;

use BuzzingPixel\CraftScheduler\Records\RecordContract;

interface SaveRecordContract
{
    public function save(RecordContract $record): void;
}
