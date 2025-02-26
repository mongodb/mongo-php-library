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
 * Returns a boolean indicating whether a specified value is in an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/in/
 * @internal
 */
final class InOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$in';
    public const PROPERTIES = ['expression' => 'expression', 'array' => 'array'];

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression Any valid expression expression. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $array Any valid expression that resolves to an array. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $array;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression Any valid expression expression.
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $array Any valid expression that resolves to an array.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
        PackedArray|ResolvesToArray|BSONArray|array|string $array,
    ) {
        $this->expression = $expression;
        if (is_string($array) && ! str_starts_with($array, '$')) {
            throw new InvalidArgumentException('Argument $array can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($array) && ! array_is_list($array)) {
            throw new InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }

        $this->array = $array;
    }
}
