<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
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
    public const PROPERTIES = ['input' => 'input', 'initialValue' => 'initialValue', 'in' => 'in'];

    /**
     * @var BSONArray|PackedArray|ResolvesToArray|array|string $input Can be any valid expression that resolves to an array.
     * If the argument resolves to a value of null or refers to a missing field, $reduce returns null.
     * If the argument does not resolve to an array or null nor refers to a missing field, $reduce returns an error.
     */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $input;

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $initialValue The initial cumulative value set before in is applied to the first element of the input array. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $initialValue;

    /**
     * @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in A valid expression that $reduce applies to each element in the input array in left-to-right order. Wrap the input value with $reverseArray to yield the equivalent of applying the combining expression from right-to-left.
     * During evaluation of the in expression, two variables will be available:
     * - value is the variable that represents the cumulative value of the expression.
     * - this is the variable that refers to the element being processed.
     */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $input Can be any valid expression that resolves to an array.
     * If the argument resolves to a value of null or refers to a missing field, $reduce returns null.
     * If the argument does not resolve to an array or null nor refers to a missing field, $reduce returns an error.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $initialValue The initial cumulative value set before in is applied to the first element of the input array.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in A valid expression that $reduce applies to each element in the input array in left-to-right order. Wrap the input value with $reverseArray to yield the equivalent of applying the combining expression from right-to-left.
     * During evaluation of the in expression, two variables will be available:
     * - value is the variable that represents the cumulative value of the expression.
     * - this is the variable that refers to the element being processed.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $input,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $initialValue,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in,
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
    }
}
