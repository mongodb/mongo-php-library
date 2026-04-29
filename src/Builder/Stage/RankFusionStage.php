<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Combines multiple pipelines using rank-based fusion to create hybrid search results.
 *
 * New in MongoDB 8.1
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rankFusion/
 * @internal
 */
final class RankFusionStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$rankFusion';
    public const PROPERTIES = ['input' => 'input', 'scoreDetails' => 'scoreDetails', 'combination' => 'combination'];

    /**
     * @var Document|Serializable|array|stdClass $input An object with the following required fields:
     * - input.pipelines: Map from name to ranked input pipeline. Each pipeline must operate on the same collection. Minimum of one pipeline.
     */
    public readonly Document|Serializable|stdClass|array $input;

    /** @var bool $scoreDetails Set to true to include detailed scoring information. */
    public readonly bool $scoreDetails;

    /**
     * @var Optional|Document|Serializable|array|stdClass $combination An object with the following optional fields:
     * - combination.weights: Map from pipeline name to numbers (non-negative). If unspecified, default weight is 1 for each pipeline.
     */
    public readonly Optional|Document|Serializable|stdClass|array $combination;

    /**
     * @param Document|Serializable|array|stdClass $input An object with the following required fields:
     * - input.pipelines: Map from name to ranked input pipeline. Each pipeline must operate on the same collection. Minimum of one pipeline.
     * @param bool $scoreDetails Set to true to include detailed scoring information.
     * @param Optional|Document|Serializable|array|stdClass $combination An object with the following optional fields:
     * - combination.weights: Map from pipeline name to numbers (non-negative). If unspecified, default weight is 1 for each pipeline.
     */
    public function __construct(
        Document|Serializable|stdClass|array $input,
        bool $scoreDetails = false,
        Optional|Document|Serializable|stdClass|array $combination = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->scoreDetails = $scoreDetails;
        $this->combination = $combination;
    }
}
