<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Returns an array with the elements in reverse order.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reverseArray/
 * @internal
 */
final class ReverseArrayOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$reverseArray';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $expression The argument can be any valid expression as long as it resolves to an array. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $expression;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $expression The argument can be any valid expression as long as it resolves to an array.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array|string $expression)
    {
        if (is_string($expression) && ! str_starts_with($expression, '$')) {
            throw new InvalidArgumentException('Argument $expression can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($expression) && ! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression argument to be a list, got an associative array.');
        }

        $this->expression = $expression;
    }
}
