<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use InvalidArgumentException;
use MongoDB\Builder\Expression\Expression;

use function count;
use function sprintf;

class MatchStage
{
    /** @param list<Expression|mixed> ...$matchExpr */
    public array $matchExpr;

    public function __construct(mixed ...$matchExpr)
    {
        if (count($matchExpr) < 1) {
            throw new InvalidArgumentException(sprintf('Expected at least %d values, got %d.', 1, count($matchExpr)));
        }

        $this->matchExpr = $matchExpr;
    }
}
