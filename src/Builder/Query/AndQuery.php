<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use InvalidArgumentException;
use MongoDB\Builder\Expression\ResolvesToBool;

use function count;
use function sprintf;

class AndQuery implements ResolvesToBool
{
    /** @param list<ResolvesToBool|bool> ...$query */
    public array $query;

    public function __construct(ResolvesToBool|bool ...$query)
    {
        if (count($query) < 1) {
            throw new InvalidArgumentException(sprintf('Expected at least %d values, got %d.', 1, count($query)));
        }

        $this->query = $query;
    }
}
