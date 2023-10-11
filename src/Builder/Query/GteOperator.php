<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

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
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\QueryFilterInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Matches values that are greater than or equal to a specified value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/gte/
 */
class GteOperator implements QueryFilterInterface
{
    public const NAME = '$gte';
    public const ENCODE = Encode::Single;

    /** @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ) {
        $this->value = $value;
    }
}
