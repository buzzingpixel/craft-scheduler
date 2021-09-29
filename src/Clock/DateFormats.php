<?php

declare(strict_types=1);

namespace BuzzingPixel\CraftScheduler\Clock;

interface DateFormats
{
    /**
     * MySQL's DateTime storage format is not compliant on input or output with
     * ISO 8601. This string represents the format MySQL uses for input/output
     * of DateTime
     */
    public const MYSQL_STORAGE_FORMAT = 'Y-m-d H:i:s';
}
