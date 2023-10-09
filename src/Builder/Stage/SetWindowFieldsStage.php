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
 * Groups documents into windows and applies one or more operators to the documents in each window.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
 */
class SetWindowFieldsStage implements StageInterface
{
    public const NAME = '$setWindowFields';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $partitionBy Specifies an expression to group the documents. In the $setWindowFields stage, the group of documents is known as a partition. Default is one partition for the entire collection. */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $partitionBy;

    /** @param Document|Serializable|array|stdClass $sortBy Specifies the field(s) to sort the documents by in the partition. Uses the same syntax as the $sort stage. Default is no sorting. */
    public Document|Serializable|stdClass|array $sortBy;

    /**
     * @param Document|Serializable|array|stdClass $output Specifies the field(s) to append to the documents in the output returned by the $setWindowFields stage. Each field is set to the result returned by the window operator.
     * A field can contain dots to specify embedded document fields and array fields. The semantics for the embedded document dotted notation in the $setWindowFields stage are the same as the $addFields and $set stages.
     */
    public Document|Serializable|stdClass|array $output;

    /** @param Document|Optional|Serializable|array|stdClass $window Specifies the window boundaries and parameters. Window boundaries are inclusive. Default is an unbounded window, which includes all documents in the partition. */
    public Document|Serializable|Optional|stdClass|array $window;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $partitionBy Specifies an expression to group the documents. In the $setWindowFields stage, the group of documents is known as a partition. Default is one partition for the entire collection.
     * @param Document|Serializable|array|stdClass $sortBy Specifies the field(s) to sort the documents by in the partition. Uses the same syntax as the $sort stage. Default is no sorting.
     * @param Document|Serializable|array|stdClass $output Specifies the field(s) to append to the documents in the output returned by the $setWindowFields stage. Each field is set to the result returned by the window operator.
     * A field can contain dots to specify embedded document fields and array fields. The semantics for the embedded document dotted notation in the $setWindowFields stage are the same as the $addFields and $set stages.
     * @param Document|Optional|Serializable|array|stdClass $window Specifies the window boundaries and parameters. Window boundaries are inclusive. Default is an unbounded window, which includes all documents in the partition.
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $partitionBy,
        Document|Serializable|stdClass|array $sortBy,
        Document|Serializable|stdClass|array $output,
        Document|Serializable|Optional|stdClass|array $window = Optional::Undefined,
    ) {
        if (\is_array($partitionBy) && ! \array_is_list($partitionBy)) {
            throw new \InvalidArgumentException('Expected $partitionBy argument to be a list, got an associative array.');
        }

        $this->partitionBy = $partitionBy;
        $this->sortBy = $sortBy;
        $this->output = $output;
        $this->window = $window;
    }
}
