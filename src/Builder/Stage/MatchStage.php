<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use InvalidArgumentException;
use MongoDB\Builder\Expression\ResolvesToMatchExpression;

use function count;
use function sprintf;

class MatchStage
{
    /** @param list<ResolvesToMatchExpression|array|object> $matchExpr */
    public array $matchExpr;

    /** @param ResolvesToMatchExpression|array|object $matchExpr */
    public function __construct(array|object ...$matchExpr)
    {
        if (count($matchExpr) < 1) {
            throw new InvalidArgumentException(sprintf('Expected at least %d values, got %d.', 1, count($matchExpr)));
        }

        $this->matchExpr = $matchExpr;
    }
}
