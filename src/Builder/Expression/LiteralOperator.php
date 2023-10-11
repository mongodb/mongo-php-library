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
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Return a value without parsing. Use for values that the aggregation pipeline may interpret as an expression. For example, use a $literal expression to a string that starts with a dollar sign ($) to avoid parsing as a field path.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/literal/
 */
class LiteralOperator implements ResolvesToAny
{
    public const NAME = '$literal';
    public const ENCODE = Encode::Single;

    /** @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression. */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression.
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ) {
        $this->value = $value;
    }
}
