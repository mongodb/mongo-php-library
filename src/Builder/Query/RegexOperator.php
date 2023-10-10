<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Selects documents where values match a specified regular expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/regex/
 */
class RegexOperator implements QueryInterface
{
    public const NAME = '$regex';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Regex $regex */
    public Regex $regex;

    /**
     * @param Regex $regex
     */
    public function __construct(Regex $regex)
    {
        $this->regex = $regex;
    }
}
