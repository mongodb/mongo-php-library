<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;

class WhereQuery implements QueryInterface
{
    public const NAME = '$where';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param non-empty-string $function */
    public string $function;

    /**
     * @param non-empty-string $function
     */
    public function __construct(string $function)
    {
        $this->function = $function;
    }
}
