<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Expression\ExpressionInterface;

class ExprQuery implements ExpressionInterface
{
    public const NAME = '$expr';
    public const ENCODE = 'single';

    public mixed $expression;

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
