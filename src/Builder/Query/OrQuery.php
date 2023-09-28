<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;

class OrQuery implements ResolvesToBool
{
    public const NAME = '$or';
    public const ENCODE = 'single';

    /** @param list<ExpressionInterface|mixed> ...$query */
    public array $query;

    /**
     * @param ExpressionInterface|mixed $query
     */
    public function __construct(mixed ...$query)
    {
        if (\count($query) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', 1, \count($query)));
        }

        $this->query = $query;
    }
}
