<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Pipeline;
use stdClass;

/**
 * Performs a left outer join to another collection in the same database to filter in documents from the "joined" collection for processing.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/
 */
class LookupStage implements StageInterface
{
    public const NAME = '$lookup';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param non-empty-string $as Specifies the name of the new array field to add to the input documents. The new array field contains the matching documents from the from collection. If the specified name already exists in the input document, the existing field is overwritten. */
    public string $as;

    /**
     * @param Optional|non-empty-string $from Specifies the collection in the same database to perform the join with.
     * from is optional, you can use a $documents stage in a $lookup stage instead. For an example, see Use a $documents Stage in a $lookup Stage.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     */
    public Optional|string $from;

    /** @param Optional|non-empty-string $localField Specifies the field from the documents input to the $lookup stage. $lookup performs an equality match on the localField to the foreignField from the documents of the from collection. If an input document does not contain the localField, the $lookup treats the field as having a value of null for matching purposes. */
    public Optional|string $localField;

    /** @param Optional|non-empty-string $foreignField Specifies the field from the documents in the from collection. $lookup performs an equality match on the foreignField to the localField from the input documents. If a document in the from collection does not contain the foreignField, the $lookup treats the value as null for matching purposes. */
    public Optional|string $foreignField;

    /** @param Document|Optional|Serializable|array|stdClass $let Specifies variables to use in the pipeline stages. Use the variable expressions to access the fields from the joined collection's documents that are input to the pipeline. */
    public Document|Serializable|Optional|stdClass|array $let;

    /**
     * @param Optional|Pipeline|array $pipeline Specifies the pipeline to run on the joined collection. The pipeline determines the resulting documents from the joined collection. To return all documents, specify an empty pipeline [].
     * The pipeline cannot include the $out stage or the $mergestage. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     * The pipeline cannot directly access the joined document fields. Instead, define variables for the joined document fields using the let option and then reference the variables in the pipeline stages.
     */
    public Optional|Pipeline|array $pipeline;

    /**
     * @param non-empty-string $as Specifies the name of the new array field to add to the input documents. The new array field contains the matching documents from the from collection. If the specified name already exists in the input document, the existing field is overwritten.
     * @param Optional|non-empty-string $from Specifies the collection in the same database to perform the join with.
     * from is optional, you can use a $documents stage in a $lookup stage instead. For an example, see Use a $documents Stage in a $lookup Stage.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     * @param Optional|non-empty-string $localField Specifies the field from the documents input to the $lookup stage. $lookup performs an equality match on the localField to the foreignField from the documents of the from collection. If an input document does not contain the localField, the $lookup treats the field as having a value of null for matching purposes.
     * @param Optional|non-empty-string $foreignField Specifies the field from the documents in the from collection. $lookup performs an equality match on the foreignField to the localField from the input documents. If a document in the from collection does not contain the foreignField, the $lookup treats the value as null for matching purposes.
     * @param Document|Optional|Serializable|array|stdClass $let Specifies variables to use in the pipeline stages. Use the variable expressions to access the fields from the joined collection's documents that are input to the pipeline.
     * @param Optional|Pipeline|array $pipeline Specifies the pipeline to run on the joined collection. The pipeline determines the resulting documents from the joined collection. To return all documents, specify an empty pipeline [].
     * The pipeline cannot include the $out stage or the $mergestage. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     * The pipeline cannot directly access the joined document fields. Instead, define variables for the joined document fields using the let option and then reference the variables in the pipeline stages.
     */
    public function __construct(
        string $as,
        Optional|string $from = Optional::Undefined,
        Optional|string $localField = Optional::Undefined,
        Optional|string $foreignField = Optional::Undefined,
        Document|Serializable|Optional|stdClass|array $let = Optional::Undefined,
        Optional|Pipeline|array $pipeline = Optional::Undefined,
    ) {
        $this->as = $as;
        $this->from = $from;
        $this->localField = $localField;
        $this->foreignField = $foreignField;
        $this->let = $let;
        $this->pipeline = $pipeline;
    }
}
