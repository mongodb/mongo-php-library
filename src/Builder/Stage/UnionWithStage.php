<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Model\BSONArray;

/**
 * Performs a union of two collections; i.e. combines pipeline results from two collections into a single result set.
 * New in version 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unionWith/
 */
class UnionWithStage implements StageInterface
{
    public const NAME = '$unionWith';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param non-empty-string $coll The collection or view whose pipeline results you wish to include in the result set. */
    public string $coll;

    /**
     * @param Optional|BSONArray|PackedArray|Pipeline|array $pipeline An aggregation pipeline to apply to the specified coll.
     * The pipeline cannot include the $out and $merge stages. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     */
    public Optional|PackedArray|Pipeline|BSONArray|array $pipeline;

    /**
     * @param non-empty-string $coll The collection or view whose pipeline results you wish to include in the result set.
     * @param Optional|BSONArray|PackedArray|Pipeline|array $pipeline An aggregation pipeline to apply to the specified coll.
     * The pipeline cannot include the $out and $merge stages. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     */
    public function __construct(
        string $coll,
        Optional|PackedArray|Pipeline|BSONArray|array $pipeline = Optional::Undefined,
    ) {
        $this->coll = $coll;
        if (\is_array($pipeline) && ! \array_is_list($pipeline)) {
            throw new \InvalidArgumentException('Expected $pipeline argument to be a list, got an associative array.');
        }

        $this->pipeline = $pipeline;
    }
}
