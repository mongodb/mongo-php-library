<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

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
    public const PROPERTIES = ['input' => 'input', 'in' => 'in', 'as' => 'as'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to an array. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $input;

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as. */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in;

    /** @var Optional|ResolvesToString|string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this. */
    public readonly Optional|ResolvesToString|string $as;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to an array.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as.
     * @param Optional|ResolvesToString|string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $input,
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
