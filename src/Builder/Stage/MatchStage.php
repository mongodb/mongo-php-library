<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Expression\ResolvesToMatchExpression;

class MatchStage
{
    /** @param list<ResolvesToMatchExpression|array|object> $matchExpr */
    public array $matchExpr;

    /** @param ResolvesToMatchExpression|array|object $matchExpr */
    public function __construct(array|object ...$matchExpr)
    {
        $this->matchExpr = $matchExpr;
    }
}
