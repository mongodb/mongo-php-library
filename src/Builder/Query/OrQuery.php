<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use InvalidArgumentException;
use MongoDB\Builder\Expression\Expression;
use MongoDB\Builder\Expression\ResolvesToBool;

use function count;
use function sprintf;

class OrQuery implements ResolvesToBool
{
    public const NAME = '$or';
    public const ENCODE = 'single';

    /** @param list<Expression|mixed> ...$query */
    public array $query;

    public function __construct(mixed ...$query)
    {
        if (count($query) < 1) {
            throw new InvalidArgumentException(sprintf('Expected at least %d values, got %d.', 1, count($query)));
        }

        $this->query = $query;
    }
}
