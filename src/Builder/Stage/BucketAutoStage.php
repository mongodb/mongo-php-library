<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use DateTimeInterface;
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Categorizes incoming documents into a specific number of groups, called buckets, based on a specified expression. Bucket boundaries are automatically determined in an attempt to evenly distribute the documents into the specified number of buckets.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucketAuto/
 * @internal
 */
final class BucketAutoStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$bucketAuto';

    public const PROPERTIES = [
        'groupBy' => 'groupBy',
        'buckets' => 'buckets',
        'output' => 'output',
        'granularity' => 'granularity',
    ];

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $groupBy;

    /** @var int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped. */
    public readonly int $buckets;

    /**
     * @var Optional|Document|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     */
    public readonly Optional|Document|Serializable|stdClass|array $output;

    /**
     * @var Optional|string $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public readonly Optional|string $granularity;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * @param int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped.
     * @param Optional|Document|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     * @param Optional|string $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $groupBy,
        int $buckets,
        Optional|Document|Serializable|stdClass|array $output = Optional::Undefined,
        Optional|string $granularity = Optional::Undefined,
    ) {
        $this->groupBy = $groupBy;
        $this->buckets = $buckets;
        $this->output = $output;
        $this->granularity = $granularity;
    }
}
