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
 * New in MongoDB 8.0
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/scoreFusion/
 * @internal
 */
final class ScoreFusionStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$scoreFusion';
    public const PROPERTIES = ['input' => 'input', 'scoreDetails' => 'scoreDetails', 'combination' => 'combination'];

    /** @var Document|Serializable|array|stdClass $input An object that specifies the pipelines to combine with score fusion. */
    public readonly Document|Serializable|stdClass|array $input;

    /** @var bool $scoreDetails Set to true to include detailed scoring information. */
    public readonly bool $scoreDetails;

    /** @var Optional|Document|Serializable|array|stdClass $combination An object that specifies how to combine the scores. */
    public readonly Optional|Document|Serializable|stdClass|array $combination;

    /**
     * @param Document|Serializable|array|stdClass $input An object that specifies the pipelines to combine with score fusion.
     * @param bool $scoreDetails Set to true to include detailed scoring information.
     * @param Optional|Document|Serializable|array|stdClass $combination An object that specifies how to combine the scores.
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
