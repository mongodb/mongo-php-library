<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Applies an expression to each element in an array and combines them into a single value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/
 * @internal
 */
final class ReduceOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$reduce';

    public const PROPERTIES = [
        'input' => 'input',
        'initialValue' => 'initialValue',
        'in' => 'in',
        'as' => 'as',
        'valueAs' => 'valueAs',
        'arrayIndexAs' => 'arrayIndexAs',
    ];

    /**
     * @var BSONArray|PackedArray|ResolvesToArray|array|string $input Can be any valid expression that resolves to an array.
     * If the argument resolves to a value of null or refers to a missing field, $reduce returns null.
     * If the argument does not resolve to an array or null nor refers to a missing field, $reduce returns an error.
     */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $input;

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $initialValue The initial cumulative value set before in is applied to the first element of the input array. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $initialValue;

    /**
     * @var DateTimeInterface|Document|ExpressionInterface|Serializable|Type|array|bool|float|int|null|stdClass|string $in A valid expression that $reduce applies to each element in the input array in left-to-right order. Wrap the input value with $reverseArray to yield the equivalent of applying the combining expression from right-to-left.
     * During evaluation of the in expression, two variables will be available:
     * - value is the variable that represents the cumulative value of the expression. Use valueAs (MongoDB 8.3+) to specify a custom name.
     * - this is the variable that refers to the element being processed. Use as (MongoDB 8.3+) to specify a custom name.
     */
    public readonly DateTimeInterface|Document|Serializable|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in;

    /**
     * @var Optional|string $as A name for the variable that represents each individual element of the input array.
     * If no name is specified, the variable name defaults to this.
     *
     * New in MongoDB 8.3
     */
    public readonly Optional|string $as;

    /**
     * @var Optional|string $valueAs A name for the variable that represents the cumulative value of the expression.
     * If no name is specified, the variable name defaults to value.
     *
     * New in MongoDB 8.3
     */
    public readonly Optional|string $valueAs;

    /**
     * @var Optional|string $arrayIndexAs A name for the variable that represents the index of the current element in
     * the input array. If specified, this variable is available within the in expression.
     *
     * New in MongoDB 8.3
     */
    public readonly Optional|string $arrayIndexAs;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $input Can be any valid expression that resolves to an array.
     * If the argument resolves to a value of null or refers to a missing field, $reduce returns null.
     * If the argument does not resolve to an array or null nor refers to a missing field, $reduce returns an error.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $initialValue The initial cumulative value set before in is applied to the first element of the input array.
     * @param DateTimeInterface|Document|ExpressionInterface|Serializable|Type|array|bool|float|int|null|stdClass|string $in A valid expression that $reduce applies to each element in the input array in left-to-right order. Wrap the input value with $reverseArray to yield the equivalent of applying the combining expression from right-to-left.
     * During evaluation of the in expression, two variables will be available:
     * - value is the variable that represents the cumulative value of the expression. Use valueAs (MongoDB 8.3+) to specify a custom name.
     * - this is the variable that refers to the element being processed. Use as (MongoDB 8.3+) to specify a custom name.
     * @param Optional|string $as A name for the variable that represents each individual element of the input array.
     * If no name is specified, the variable name defaults to this.
     *
     * New in MongoDB 8.3
     * @param Optional|string $valueAs A name for the variable that represents the cumulative value of the expression.
     * If no name is specified, the variable name defaults to value.
     *
     * New in MongoDB 8.3
     * @param Optional|string $arrayIndexAs A name for the variable that represents the index of the current element in
     * the input array. If specified, this variable is available within the in expression.
     *
     * New in MongoDB 8.3
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $input,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $initialValue,
        DateTimeInterface|Document|Serializable|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in,
        Optional|string $as = Optional::Undefined,
        Optional|string $valueAs = Optional::Undefined,
        Optional|string $arrayIndexAs = Optional::Undefined,
    ) {
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        $this->initialValue = $initialValue;
        $this->in = $in;
        $this->as = $as;
        $this->valueAs = $valueAs;
        $this->arrayIndexAs = $arrayIndexAs;
    }
}
