<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Pipeline;

class UnionWithStage implements StageInterface
{
    public const NAME = '$unionWith';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param non-empty-string $coll The collection or view whose pipeline results you wish to include in the result set. */
    public string $coll;

    /**
     * @param Optional|Pipeline|array $pipeline An aggregation pipeline to apply to the specified coll.
     * The pipeline cannot include the $out and $merge stages. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     */
    public Optional|Pipeline|array $pipeline;

    /**
     * @param non-empty-string $coll The collection or view whose pipeline results you wish to include in the result set.
     * @param Optional|Pipeline|array $pipeline An aggregation pipeline to apply to the specified coll.
     * The pipeline cannot include the $out and $merge stages. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     */
    public function __construct(string $coll, Optional|Pipeline|array $pipeline = Optional::Undefined)
    {
        $this->coll = $coll;
        $this->pipeline = $pipeline;
    }
}
