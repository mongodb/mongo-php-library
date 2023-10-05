<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use stdClass;

class NotQuery implements QueryInterface
{
    public const NAME = '$not';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param QueryInterface|array|stdClass $expression */
    public QueryInterface|stdClass|array $expression;

    /**
     * @param QueryInterface|array|stdClass $expression
     */
    public function __construct(QueryInterface|stdClass|array $expression)
    {
        $this->expression = $expression;
    }
}
