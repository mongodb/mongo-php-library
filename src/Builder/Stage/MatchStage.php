<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Expression\ExpressionInterface;

class MatchStage implements StageInterface
{
    public const NAME = '$match';
    public const ENCODE = 'single';

    public mixed $query;

    /**
     * @param ExpressionInterface|mixed $query
     */
    public function __construct(mixed $query)
    {
        $this->query = $query;
    }
}
