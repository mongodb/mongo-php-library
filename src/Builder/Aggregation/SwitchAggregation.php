<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Optional;
use MongoDB\Model\BSONArray;

/**
 * Evaluates a series of case expressions. When it finds an expression which evaluates to true, $switch executes a specified expression and breaks out of the control flow.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/
 */
class SwitchAggregation implements ExpressionInterface
{
    public const NAME = '$switch';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param BSONArray|PackedArray|list $branches An array of control branch documents. Each branch is a document with the following fields:
     * - case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * - then Can be any valid expression.
     * The branches array must contain at least one branch document.
     */
    public PackedArray|BSONArray|array $branches;

    /**
     * @param ExpressionInterface|Optional|mixed $default The path to take if no branch case expression evaluates to true.
     * Although optional, if default is unspecified and no branch case evaluates to true, $switch returns an error.
     */
    public mixed $default;

    /**
     * @param BSONArray|PackedArray|list $branches An array of control branch documents. Each branch is a document with the following fields:
     * - case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * - then Can be any valid expression.
     * The branches array must contain at least one branch document.
     * @param ExpressionInterface|Optional|mixed $default The path to take if no branch case expression evaluates to true.
     * Although optional, if default is unspecified and no branch case evaluates to true, $switch returns an error.
     */
    public function __construct(PackedArray|BSONArray|array $branches, mixed $default = Optional::Undefined)
    {
        if (\is_array($branches) && ! \array_is_list($branches)) {
            throw new \InvalidArgumentException('Expected $branches argument to be a list, got an associative array.');
        }
        $this->branches = $branches;
        $this->default = $default;
    }
}
