<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\ExpressionInterface;

/**
 * Generates a random float between 0 and 1.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/rand/
 */
class RandQuery implements ExpressionInterface
{
    public const NAME = '$rand';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    public function __construct()
    {
    }
}
