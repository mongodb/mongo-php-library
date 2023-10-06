<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Model\BSONArray;

/**
 * Applies an expression to each element in an array and combines them into a single value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/
 */
class ReduceAggregation implements ExpressionInterface
{
    public const NAME = '$reduce';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list $input Can be any valid expression that resolves to an array.
     * If the argument resolves to a value of null or refers to a missing field, $reduce returns null.
     * If the argument does not resolve to an array or null nor refers to a missing field, $reduce returns an error.
     */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param ExpressionInterface|mixed $initialValue The initial cumulative value set before in is applied to the first element of the input array. */
    public mixed $initialValue;

    /**
     * @param ExpressionInterface|mixed $in A valid expression that $reduce applies to each element in the input array in left-to-right order. Wrap the input value with $reverseArray to yield the equivalent of applying the combining expression from right-to-left.
     * During evaluation of the in expression, two variables will be available:
     * - value is the variable that represents the cumulative value of the expression.
     * - this is the variable that refers to the element being processed.
     */
    public mixed $in;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list $input Can be any valid expression that resolves to an array.
     * If the argument resolves to a value of null or refers to a missing field, $reduce returns null.
     * If the argument does not resolve to an array or null nor refers to a missing field, $reduce returns an error.
     * @param ExpressionInterface|mixed $initialValue The initial cumulative value set before in is applied to the first element of the input array.
     * @param ExpressionInterface|mixed $in A valid expression that $reduce applies to each element in the input array in left-to-right order. Wrap the input value with $reverseArray to yield the equivalent of applying the combining expression from right-to-left.
     * During evaluation of the in expression, two variables will be available:
     * - value is the variable that represents the cumulative value of the expression.
     * - this is the variable that refers to the element being processed.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $input, mixed $initialValue, mixed $in)
    {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }
        $this->input = $input;
        $this->initialValue = $initialValue;
        $this->in = $in;
    }
}
