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
 * Combines multiple pipelines using relative score fusion to create hybrid search results.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/scoreFusion/
 * @internal
 */
final class ScoreFusionStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$scoreFusion';
    public const PROPERTIES = ['input' => 'input', 'scoreDetails' => 'scoreDetails', 'combination' => 'combination'];

    /**
     * @var Document|Serializable|array|stdClass $input An object with the following required fields:
     * - input.pipelines: Map from name to input pipeline. Each pipeline must be operating on the same collection. Minimum of one pipeline.
     * - input.normalization: Normalizes the score to the range 0 to 1 before combining the results. Value can be none, sigmoid or minMaxScaler.
     */
    public readonly Document|Serializable|stdClass|array $input;

    /** @var bool $scoreDetails Set to true to include detailed scoring information. */
    public readonly bool $scoreDetails;

    /**
     * @var Optional|Document|Serializable|array|stdClass $combination An object with the following optional fields:
     * - combination.weights: Map from pipeline name to numbers (non-negative). If unspecified, default weight is 1 for each pipeline.
     * - combination.method: Specifies method for combining scores. Value can be avg or expression. Default is avg.
     * - combination.expression: This is the custom expression that is used when combination.method is set to expression.
     */
    public readonly Optional|Document|Serializable|stdClass|array $combination;

    /**
     * @param Document|Serializable|array|stdClass $input An object with the following required fields:
     * - input.pipelines: Map from name to input pipeline. Each pipeline must be operating on the same collection. Minimum of one pipeline.
     * - input.normalization: Normalizes the score to the range 0 to 1 before combining the results. Value can be none, sigmoid or minMaxScaler.
     * @param bool $scoreDetails Set to true to include detailed scoring information.
     * @param Optional|Document|Serializable|array|stdClass $combination An object with the following optional fields:
     * - combination.weights: Map from pipeline name to numbers (non-negative). If unspecified, default weight is 1 for each pipeline.
     * - combination.method: Specifies method for combining scores. Value can be avg or expression. Default is avg.
     * - combination.expression: This is the custom expression that is used when combination.method is set to expression.
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
