<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;

/**
 * A special hint that can be provided via the sort() or hint() methods that can be used to force either a forward or reverse collection scan.
 *
 * @see https://www.mongodb.com/docs/v7.0/reference/operator/meta/natural/
 */
readonly class NaturalOperator implements ExpressionInterface
{
    public const NAME = '$natural';
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }
}
