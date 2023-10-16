<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Applies a subexpression to each element of an array and returns the array of resulting values in order. Accepts named parameters.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/
 */
readonly class MapOperator implements ResolvesToArray
{
    public const NAME = '$map';
    public const ENCODE = Encode::Object;

    /** @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as. */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in;

    /** @param Optional|ResolvesToString|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this. */
    public Optional|ResolvesToString|string $as;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to an array.
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as.
     * @param Optional|ResolvesToString|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in,
        Optional|ResolvesToString|string $as = Optional::Undefined,
    ) {
        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        $this->in = $in;
        $this->as = $as;
    }
}
