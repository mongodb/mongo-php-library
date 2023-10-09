<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

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
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Categorizes incoming documents into a specific number of groups, called buckets, based on a specified expression. Bucket boundaries are automatically determined in an attempt to evenly distribute the documents into the specified number of buckets.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucketAuto/
 */
class BucketAutoStage implements StageInterface
{
    public const NAME = '$bucketAuto';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes. */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $groupBy;

    /** @param int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped. */
    public int $buckets;

    /**
     * @param Document|Optional|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     */
    public Document|Serializable|Optional|stdClass|array $output;

    /**
     * @param Optional|non-empty-string $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public Optional|string $granularity;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * @param int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped.
     * @param Document|Optional|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     * @param Optional|non-empty-string $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $groupBy,
        int $buckets,
        Document|Serializable|Optional|stdClass|array $output = Optional::Undefined,
        Optional|string $granularity = Optional::Undefined,
    ) {
        if (\is_array($groupBy) && ! \array_is_list($groupBy)) {
            throw new \InvalidArgumentException('Expected $groupBy argument to be a list, got an associative array.');
        }

        $this->groupBy = $groupBy;
        $this->buckets = $buckets;
        $this->output = $output;
        $this->granularity = $granularity;
    }
}
