<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\RecordRetrieval;

use craft\db\Query;

use function array_map;
use function call_user_func;

class RetrieveRecords
{
    /**
     * @param class-string $classString Must implement RecordContract
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function retrieve(
        string $classString,
        Query $query
    ): RecordCollection {
        /** @phpstan-ignore-next-line  */
        $table = (string) call_user_func($classString . '::table');

        $query = $query->from($table);

        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress MissingClosureReturnType
         */
        return new RecordCollection(array_map(
            static fn (array $r) => call_user_func(
            /** @phpstan-ignore-next-line */
                $classString . '::fromArray',
                $r,
            ),
            $query->all(),
        ));
    }
}
