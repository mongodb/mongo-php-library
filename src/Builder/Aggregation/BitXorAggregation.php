<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;

/**
 * Returns the result of a bitwise xor (exclusive or) operation on an array of int and long values.
 * New in version 6.3.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitXor/
 */
class BitXorAggregation implements ResolvesToInt, ResolvesToLong
{
    public const NAME = '$bitXor';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    public function __construct()
    {
    }
}
