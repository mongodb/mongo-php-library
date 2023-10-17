<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * A special hint that can be provided via the sort() or hint() methods that can be used to force either a forward or reverse collection scan.
 *
 * @see https://www.mongodb.com/docs/v7.0/reference/operator/meta/natural/
 */
class NaturalOperator implements ExpressionInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }

    public function getOperator(): string
    {
        return '$natural';
    }
}
