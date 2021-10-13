<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\migrations;

use craft\db\Migration;
use DirectoryIterator;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

use function array_reverse;
use function ksort;

/**
 * @codeCoverageIgnore
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Install extends Migration
{
    /**
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function safeUp(): bool
    {
        foreach ($this->getMigrationClasses() as $migrationClass) {
            if (! $migrationClass->safeUp()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function safeDown(): bool
    {
        $migrationClasses = $this->getMigrationClasses();

        $migrationClasses = array_reverse($migrationClasses);

        foreach ($migrationClasses as $migrationClass) {
            if (! $migrationClass->safeDown()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return Migration[]
     *
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    private function getMigrationClasses(): array
    {
        $classes = [];

        foreach (new DirectoryIterator(__DIR__) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $fileName = $fileInfo->getBasename('.php');

            if ($fileName === 'Install') {
                continue;
            }

            $class = 'BuzzingPixel\\CraftScheduler\\migrations\\' . $fileName;

            /**
             * @phpstan-ignore-next-line
             */
            $classes[$class] = Yii::$container->get($class);
        }

        ksort($classes);

        return $classes;
    }
}
