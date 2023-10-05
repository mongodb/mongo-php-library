<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Optional;
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

    /** @param ExpressionInterface|mixed $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes. */
    public mixed $groupBy;

    /** @param Int64|int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped. */
    public Int64|int $buckets;

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
     * @param ExpressionInterface|mixed $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * @param Int64|int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped.
     * @param Document|Optional|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     * @param Optional|non-empty-string $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public function __construct(
        mixed $groupBy,
        Int64|int $buckets,
        Document|Serializable|Optional|stdClass|array $output = Optional::Undefined,
        Optional|string $granularity = Optional::Undefined,
    ) {
        $this->groupBy = $groupBy;
        $this->buckets = $buckets;
        $this->output = $output;
        $this->granularity = $granularity;
    }
}