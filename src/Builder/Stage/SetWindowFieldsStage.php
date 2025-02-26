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
 * Groups documents into windows and applies one or more operators to the documents in each window.
 * New in MongoDB 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
 * @internal
 */
final class SetWindowFieldsStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$setWindowFields';
    public const PROPERTIES = ['sortBy' => 'sortBy', 'output' => 'output', 'partitionBy' => 'partitionBy'];

    /** @var Document|Serializable|array|stdClass $sortBy Specifies the field(s) to sort the documents by in the partition. Uses the same syntax as the $sort stage. Default is no sorting. */
    public readonly Document|Serializable|stdClass|array $sortBy;

    /**
     * @var Document|Serializable|array|stdClass $output Specifies the field(s) to append to the documents in the output returned by the $setWindowFields stage. Each field is set to the result returned by the window operator.
     * A field can contain dots to specify embedded document fields and array fields. The semantics for the embedded document dotted notation in the $setWindowFields stage are the same as the $addFields and $set stages.
     */
    public readonly Document|Serializable|stdClass|array $output;

    /** @var Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $partitionBy Specifies an expression to group the documents. In the $setWindowFields stage, the group of documents is known as a partition. Default is one partition for the entire collection. */
    public readonly Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $partitionBy;

    /**
     * @param Document|Serializable|array|stdClass $sortBy Specifies the field(s) to sort the documents by in the partition. Uses the same syntax as the $sort stage. Default is no sorting.
     * @param Document|Serializable|array|stdClass $output Specifies the field(s) to append to the documents in the output returned by the $setWindowFields stage. Each field is set to the result returned by the window operator.
     * A field can contain dots to specify embedded document fields and array fields. The semantics for the embedded document dotted notation in the $setWindowFields stage are the same as the $addFields and $set stages.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $partitionBy Specifies an expression to group the documents. In the $setWindowFields stage, the group of documents is known as a partition. Default is one partition for the entire collection.
     */
    public function __construct(
        Document|Serializable|stdClass|array $sortBy,
        Document|Serializable|stdClass|array $output,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $partitionBy = Optional::Undefined,
    ) {
        $this->sortBy = $sortBy;
        $this->output = $output;
        $this->partitionBy = $partitionBy;
    }
}
