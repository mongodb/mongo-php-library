<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Categorizes incoming documents into a specific number of groups, called buckets, based on a specified expression. Bucket boundaries are automatically determined in an attempt to evenly distribute the documents into the specified number of buckets.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucketAuto/
 */
readonly class BucketAutoStage implements StageInterface
{
    public const NAME = '$bucketAuto';
    public const ENCODE = Encode::Object;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes. */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $groupBy;

    /** @param int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped. */
    public int $buckets;

    /**
     * @param Optional|Document|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     */
    public Optional|Document|Serializable|stdClass|array $output;

    /**
     * @param Optional|Document|Serializable|array|stdClass $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public Optional|Document|Serializable|stdClass|array $granularity;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * @param int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped.
     * @param Optional|Document|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     * @param Optional|Document|Serializable|array|stdClass $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public function __construct(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $groupBy,
        int $buckets,
        Optional|Document|Serializable|stdClass|array $output = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $granularity = Optional::Undefined,
    ) {
        $this->groupBy = $groupBy;
        $this->buckets = $buckets;
        $this->output = $output;
        $this->granularity = $granularity;
    }
}
