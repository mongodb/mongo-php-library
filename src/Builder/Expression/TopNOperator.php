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
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Returns an aggregation of the top n elements within an array, according to the specified sort order.
 * If the array contains fewer than n elements, $topN returns all elements in the array.
 *
 * New in MongoDB 7.0
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN-array-operator/
 * @internal
 */
final class TopNOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$topN';
    public const PROPERTIES = ['n' => 'n', 'sortBy' => 'sortBy', 'output' => 'output', 'input' => 'input'];

    /** @var ResolvesToInt|int|string $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $topN returns. */
    public readonly ResolvesToInt|int|string $n;

    /** @var Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort. */
    public readonly Document|Serializable|stdClass|array $sortBy;

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $output Represents the output for each element in the input array and can be any expression. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output;

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to the array from which to return the top n elements. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $input;

    /**
     * @param ResolvesToInt|int|string $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $topN returns.
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $output Represents the output for each element in the input array and can be any expression.
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to the array from which to return the top n elements.
     */
    public function __construct(
        ResolvesToInt|int|string $n,
        Document|Serializable|stdClass|array $sortBy,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output,
        PackedArray|ResolvesToArray|BSONArray|array|string $input,
    ) {
        if (is_string($n) && ! str_starts_with($n, '$')) {
            throw new InvalidArgumentException('Argument $n can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->n = $n;
        $this->sortBy = $sortBy;
        $this->output = $output;
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
    }
}
