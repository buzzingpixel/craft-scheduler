<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordPersistence;

use BuzzingPixel\CraftScheduler\Records\RecordContract;
use craft\db\Connection;
use yii\db\Exception;

class SaveExistingRecord implements SaveRecordContract
{
    public function __construct(private Connection $dbConnection)
    {
    }

    /**
     * @throws Exception
     */
    public function save(RecordContract $record): void
    {
        $this->dbConnection->createCommand()
            ->update(
                $record->tableName(),
                $record->asArray(),
                '`id` = :id',
                ['id' => $record->id()],
            )
            ->execute();
    }
}
