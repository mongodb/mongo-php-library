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

/**
 * Returns true if any elements of a set evaluate to true; otherwise, returns false. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/anyElementTrue/
 */
class AnyElementTrueOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Array;

    /** @var BSONArray|PackedArray|ResolvesToArray|array $expression */
    public readonly PackedArray|ResolvesToArray|BSONArray|array $expression;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $expression)
    {
        if (is_array($expression) && ! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression argument to be a list, got an associative array.');
        }

        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$anyElementTrue';
    }
}
