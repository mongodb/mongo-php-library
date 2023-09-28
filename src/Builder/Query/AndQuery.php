<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Expression\ResolvesToBool;

class AndQuery implements ResolvesToBool
{
    public const NAME = '$and';
    public const ENCODE = 'single';

    /** @param list<ResolvesToBool|bool> ...$query */
    public array $query;

    /**
     * @param ResolvesToBool|bool $query
     */
    public function __construct(ResolvesToBool|bool ...$query)
    {
        if (\count($query) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', 1, \count($query)));
        }

        $this->query = $query;
    }
}
