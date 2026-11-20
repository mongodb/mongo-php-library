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
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Applies a subexpression to each element of an array and returns the array of resulting values in order. Accepts named parameters.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/
 * @internal
 */
final class MapOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$map';
    public const PROPERTIES = ['input' => 'input', 'in' => 'in', 'as' => 'as', 'arrayIndexAs' => 'arrayIndexAs'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to an array. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $input;

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in;

    /** @var Optional|string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this. */
    public readonly Optional|string $as;

    /**
     * @var Optional|string $arrayIndexAs A name for the variable that represents the index of the current element in
     * the input array. If specified, this variable is available within the in expression.
     *
     * New in MongoDB 8.3
     */
    public readonly Optional|string $arrayIndexAs;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to an array.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as.
     * @param Optional|string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     * @param Optional|string $arrayIndexAs A name for the variable that represents the index of the current element in
     * the input array. If specified, this variable is available within the in expression.
     *
     * New in MongoDB 8.3
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $input,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in,
        Optional|string $as = Optional::Undefined,
        Optional|string $arrayIndexAs = Optional::Undefined,
    ) {
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        $this->in = $in;
        $this->as = $as;
        $this->arrayIndexAs = $arrayIndexAs;
    }
}
