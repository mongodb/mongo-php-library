<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Computes and returns a new score as metadata. It also optionally normalizes the input
 * scores, by default to a range between zero and one.
 *
 * New in MongoDB 8.2
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/score/
 * @internal
 */
final class ScoreStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$score';

    public const PROPERTIES = [
        'score' => 'score',
        'normalization' => 'normalization',
        'weight' => 'weight',
        'scoreDetails' => 'scoreDetails',
    ];

    /**
     * @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $score Computes a new value from the input scores and stores the value in the $meta keyword
     * score. Returns an error for non-numeric inputs.
     */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $score;

    /**
     * @var Optional|string $normalization Normalizes the score to the range of 0 to 1. Value can be:
     * - none: Doesn't normalize. If omitted, defaults to none.
     * - sigmoid: Applies the sigmoid expression: 1 / (1 + e^-x).
     * - minMaxScaler: Applies the $minMaxScaler window function.
     */
    public readonly Optional|string $normalization;

    /** @var Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $weight Number to multiply the score expression by after normalization. */
    public readonly Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $weight;

    /**
     * @var Optional|bool $scoreDetails Specifies if $score computes and populates the $scoreDetails metadata field for each
     * output document.
     */
    public readonly Optional|bool $scoreDetails;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $score Computes a new value from the input scores and stores the value in the $meta keyword
     * score. Returns an error for non-numeric inputs.
     * @param Optional|string $normalization Normalizes the score to the range of 0 to 1. Value can be:
     * - none: Doesn't normalize. If omitted, defaults to none.
     * - sigmoid: Applies the sigmoid expression: 1 / (1 + e^-x).
     * - minMaxScaler: Applies the $minMaxScaler window function.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $weight Number to multiply the score expression by after normalization.
     * @param Optional|bool $scoreDetails Specifies if $score computes and populates the $scoreDetails metadata field for each
     * output document.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $score,
        Optional|string $normalization = Optional::Undefined,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $weight = Optional::Undefined,
        Optional|bool $scoreDetails = Optional::Undefined,
    ) {
        $this->score = $score;
        $this->normalization = $normalization;
        $this->weight = $weight;
        $this->scoreDetails = $scoreDetails;
    }
}
