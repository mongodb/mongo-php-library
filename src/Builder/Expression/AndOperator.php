<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;

/**
 * Returns true only when all its expressions evaluate to true. Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/and/
 */
readonly class AndOperator implements ResolvesToBool
{
    public const NAME = '$and';
    public const ENCODE = Encode::Single;

    /** @param list<BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToBool|ResolvesToInt|ResolvesToNull|ResolvesToNumber|ResolvesToString|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass> ...$expression */
    public array $expression;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToBool|ResolvesToInt|ResolvesToNull|ResolvesToNumber|ResolvesToString|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass ...$expression
     * @no-named-arguments
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToBool|ResolvesToInt|ResolvesToNull|ResolvesToNumber|ResolvesToString|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string ...$expression,
    ) {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
