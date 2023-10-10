<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Populates null and missing field values within documents.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/
 */
class FillStage implements StageInterface
{
    public const NAME = '$fill';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param Document|Serializable|array|stdClass $output Specifies an object containing each field for which to fill missing values. You can specify multiple fields in the output object.
     * The object name is the name of the field to fill. The object value specifies how the field is filled.
     */
    public Document|Serializable|stdClass|array $output;

    /**
     * @param Optional|Document|Serializable|array|non-empty-string|stdClass $partitionBy Specifies an expression to group the documents. In the $fill stage, a group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     */
    public Optional|Document|Serializable|stdClass|array|string $partitionBy;

    /**
     * @param Optional|BSONArray|PackedArray|array $partitionByFields Specifies an array of fields as the compound key to group the documents. In the $fill stage, each group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     */
    public Optional|PackedArray|BSONArray|array $partitionByFields;

    /** @param Optional|Document|Serializable|array|stdClass $sortBy Specifies the field or fields to sort the documents within each partition. Uses the same syntax as the $sort stage. */
    public Optional|Document|Serializable|stdClass|array $sortBy;

    /**
     * @param Document|Serializable|array|stdClass $output Specifies an object containing each field for which to fill missing values. You can specify multiple fields in the output object.
     * The object name is the name of the field to fill. The object value specifies how the field is filled.
     * @param Optional|Document|Serializable|array|non-empty-string|stdClass $partitionBy Specifies an expression to group the documents. In the $fill stage, a group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     * @param Optional|BSONArray|PackedArray|array $partitionByFields Specifies an array of fields as the compound key to group the documents. In the $fill stage, each group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     * @param Optional|Document|Serializable|array|stdClass $sortBy Specifies the field or fields to sort the documents within each partition. Uses the same syntax as the $sort stage.
     */
    public function __construct(
        Document|Serializable|stdClass|array $output,
        Optional|Document|Serializable|stdClass|array|string $partitionBy = Optional::Undefined,
        Optional|PackedArray|BSONArray|array $partitionByFields = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $sortBy = Optional::Undefined,
    ) {
        $this->output = $output;
        $this->partitionBy = $partitionBy;
        if (\is_array($partitionByFields) && ! \array_is_list($partitionByFields)) {
            throw new \InvalidArgumentException('Expected $partitionByFields argument to be a list, got an associative array.');
        }

        $this->partitionByFields = $partitionByFields;
        $this->sortBy = $sortBy;
    }
}
