<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\migrations;

use BuzzingPixel\CraftScheduler\Records\ScheduleTrackingRecord;
use craft\db\Migration;

// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps

/**
 * @codeCoverageIgnore
 * @psalm-suppress PropertyNotSetInConstructor
 */
class m210929_174645_AddScheduleTrackingTable extends Migration
{
    public function safeUp(): bool
    {
        if (
            $this->getDb()->tableExists(
                ScheduleTrackingRecord::table()
            )
        ) {
            return true;
        }

        $this->createTable(ScheduleTrackingRecord::table(), [
            'id' => $this->primaryKey(),
            'class_name' => $this->text()->notNull(),
            'method' => $this->tinyText()->notNull(),
            'run_every' => $this->tinyText()->notNull(),
            'is_running' => $this->boolean()->notNull(),
            'last_run_start_at' => $this->dateTime()->null(),
            'last_run_end_at' => $this->dateTime()->null(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists(ScheduleTrackingRecord::table());

        return true;
    }
}
