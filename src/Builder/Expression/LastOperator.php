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
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Returns the result of an expression for the last document in a group or window.
 * Changed in version 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/
 */
class LastOperator implements AccumulatorInterface, WindowInterface
{
    public const NAME = '$last';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ) {
        $this->expression = $expression;
    }
}
