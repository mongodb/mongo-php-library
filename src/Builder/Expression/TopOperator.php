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
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Returns the top element within a group according to the specified sort order.
 * New in version 5.2.
 *
 * Available in the $group and $setWindowFields stages.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/top/
 */
class TopOperator implements AccumulatorInterface
{
    public const NAME = '$top';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort. */
    public Document|Serializable|stdClass|array $sortBy;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression. */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $output;

    /**
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression.
     */
    public function __construct(
        Document|Serializable|stdClass|array $sortBy,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $output,
    ) {
        $this->sortBy = $sortBy;
        $this->output = $output;
    }
}