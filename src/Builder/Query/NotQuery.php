<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;

class NotQuery implements QueryInterface
{
    public const NAME = '$not';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param QueryInterface|array|object $expression */
    public array|object $expression;

    /**
     * @param QueryInterface|array|object $expression
     */
    public function __construct(array|object $expression)
    {
        $this->expression = $expression;
    }
}
