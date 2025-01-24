<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Populates null and missing field values within documents.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/
 * @internal
 */
final class FillStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$fill';

    public const PROPERTIES = [
        'output' => 'output',
        'partitionBy' => 'partitionBy',
        'partitionByFields' => 'partitionByFields',
        'sortBy' => 'sortBy',
    ];

    /**
     * @var Document|Serializable|array|stdClass $output Specifies an object containing each field for which to fill missing values. You can specify multiple fields in the output object.
     * The object name is the name of the field to fill. The object value specifies how the field is filled.
     */
    public readonly Document|Serializable|stdClass|array $output;

    /**
     * @var Optional|Document|Serializable|array|stdClass|string $partitionBy Specifies an expression to group the documents. In the $fill stage, a group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     */
    public readonly Optional|Document|Serializable|stdClass|array|string $partitionBy;

    /**
     * @var Optional|BSONArray|PackedArray|array $partitionByFields Specifies an array of fields as the compound key to group the documents. In the $fill stage, each group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     */
    public readonly Optional|PackedArray|BSONArray|array $partitionByFields;

    /** @var Optional|Document|Serializable|array|stdClass $sortBy Specifies the field or fields to sort the documents within each partition. Uses the same syntax as the $sort stage. */
    public readonly Optional|Document|Serializable|stdClass|array $sortBy;

    /**
     * @param Document|Serializable|array|stdClass $output Specifies an object containing each field for which to fill missing values. You can specify multiple fields in the output object.
     * The object name is the name of the field to fill. The object value specifies how the field is filled.
     * @param Optional|Document|Serializable|array|stdClass|string $partitionBy Specifies an expression to group the documents. In the $fill stage, a group of documents is known as a partition.
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
        if (is_array($partitionByFields) && ! array_is_list($partitionByFields)) {
            throw new InvalidArgumentException('Expected $partitionByFields argument to be a list, got an associative array.');
        }

        $this->partitionByFields = $partitionByFields;
        $this->sortBy = $sortBy;
    }
}
