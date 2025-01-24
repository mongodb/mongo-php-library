<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Pipeline;
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
 * Performs a left outer join to another collection in the same database to filter in documents from the "joined" collection for processing.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/
 * @internal
 */
final class LookupStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$lookup';

    public const PROPERTIES = [
        'as' => 'as',
        'from' => 'from',
        'localField' => 'localField',
        'foreignField' => 'foreignField',
        'let' => 'let',
        'pipeline' => 'pipeline',
    ];

    /** @var string $as Specifies the name of the new array field to add to the input documents. The new array field contains the matching documents from the from collection. If the specified name already exists in the input document, the existing field is overwritten. */
    public readonly string $as;

    /**
     * @var Optional|string $from Specifies the collection in the same database to perform the join with.
     * from is optional, you can use a $documents stage in a $lookup stage instead. For an example, see Use a $documents Stage in a $lookup Stage.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     */
    public readonly Optional|string $from;

    /** @var Optional|string $localField Specifies the field from the documents input to the $lookup stage. $lookup performs an equality match on the localField to the foreignField from the documents of the from collection. If an input document does not contain the localField, the $lookup treats the field as having a value of null for matching purposes. */
    public readonly Optional|string $localField;

    /** @var Optional|string $foreignField Specifies the field from the documents in the from collection. $lookup performs an equality match on the foreignField to the localField from the input documents. If a document in the from collection does not contain the foreignField, the $lookup treats the value as null for matching purposes. */
    public readonly Optional|string $foreignField;

    /** @var Optional|Document|Serializable|array|stdClass $let Specifies variables to use in the pipeline stages. Use the variable expressions to access the fields from the joined collection's documents that are input to the pipeline. */
    public readonly Optional|Document|Serializable|stdClass|array $let;

    /**
     * @var Optional|BSONArray|PackedArray|Pipeline|array $pipeline Specifies the pipeline to run on the joined collection. The pipeline determines the resulting documents from the joined collection. To return all documents, specify an empty pipeline [].
     * The pipeline cannot include the $out stage or the $merge stage. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     * The pipeline cannot directly access the joined document fields. Instead, define variables for the joined document fields using the let option and then reference the variables in the pipeline stages.
     */
    public readonly Optional|PackedArray|Pipeline|BSONArray|array $pipeline;

    /**
     * @param string $as Specifies the name of the new array field to add to the input documents. The new array field contains the matching documents from the from collection. If the specified name already exists in the input document, the existing field is overwritten.
     * @param Optional|string $from Specifies the collection in the same database to perform the join with.
     * from is optional, you can use a $documents stage in a $lookup stage instead. For an example, see Use a $documents Stage in a $lookup Stage.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     * @param Optional|string $localField Specifies the field from the documents input to the $lookup stage. $lookup performs an equality match on the localField to the foreignField from the documents of the from collection. If an input document does not contain the localField, the $lookup treats the field as having a value of null for matching purposes.
     * @param Optional|string $foreignField Specifies the field from the documents in the from collection. $lookup performs an equality match on the foreignField to the localField from the input documents. If a document in the from collection does not contain the foreignField, the $lookup treats the value as null for matching purposes.
     * @param Optional|Document|Serializable|array|stdClass $let Specifies variables to use in the pipeline stages. Use the variable expressions to access the fields from the joined collection's documents that are input to the pipeline.
     * @param Optional|BSONArray|PackedArray|Pipeline|array $pipeline Specifies the pipeline to run on the joined collection. The pipeline determines the resulting documents from the joined collection. To return all documents, specify an empty pipeline [].
     * The pipeline cannot include the $out stage or the $merge stage. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     * The pipeline cannot directly access the joined document fields. Instead, define variables for the joined document fields using the let option and then reference the variables in the pipeline stages.
     */
    public function __construct(
        string $as,
        Optional|string $from = Optional::Undefined,
        Optional|string $localField = Optional::Undefined,
        Optional|string $foreignField = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $let = Optional::Undefined,
        Optional|PackedArray|Pipeline|BSONArray|array $pipeline = Optional::Undefined,
    ) {
        $this->as = $as;
        $this->from = $from;
        $this->localField = $localField;
        $this->foreignField = $foreignField;
        $this->let = $let;
        if (is_array($pipeline) && ! array_is_list($pipeline)) {
            throw new InvalidArgumentException('Expected $pipeline argument to be a list, got an associative array.');
        }

        $this->pipeline = $pipeline;
    }
}
