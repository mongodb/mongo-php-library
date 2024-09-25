<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

/**
 * Values for "unit" property of stages like $derivative and $integral, and operators like $dateAdd and $dateDiff
 */
enum TimeUnit: string implements DictionaryInterface
{
    case Year = 'year';
    case Quarter = 'quarter';
    case Week = 'week';
    case Month = 'month';
    case Day = 'day';
    case Hour = 'hour';
    case Minute = 'minute';
    case Second = 'second';
    case Millisecond = 'millisecond';

    public function getValue(): string
    {
        return $this->value;
    }
}
