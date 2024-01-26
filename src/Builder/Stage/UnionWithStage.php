<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Performs a union of two collections; i.e. combines pipeline results from two collections into a single result set.
 * New in MongoDB 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unionWith/
 */
class UnionWithStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var string $coll The collection or view whose pipeline results you wish to include in the result set. */
    public readonly string $coll;

    /**
     * @var Optional|BSONArray|PackedArray|Pipeline|array $pipeline An aggregation pipeline to apply to the specified coll.
     * The pipeline cannot include the $out and $merge stages. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     */
    public readonly Optional|PackedArray|Pipeline|BSONArray|array $pipeline;

    /**
     * @param string $coll The collection or view whose pipeline results you wish to include in the result set.
     * @param Optional|BSONArray|PackedArray|Pipeline|array $pipeline An aggregation pipeline to apply to the specified coll.
     * The pipeline cannot include the $out and $merge stages. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     */
    public function __construct(
        string $coll,
        Optional|PackedArray|Pipeline|BSONArray|array $pipeline = Optional::Undefined,
    ) {
        $this->coll = $coll;
        if (is_array($pipeline) && ! array_is_list($pipeline)) {
            throw new InvalidArgumentException('Expected $pipeline argument to be a list, got an associative array.');
        }

        $this->pipeline = $pipeline;
    }

    public function getOperator(): string
    {
        return '$unionWith';
    }
}
