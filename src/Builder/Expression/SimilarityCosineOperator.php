<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function count;
use function is_array;
use function is_countable;
use function is_string;
use function sprintf;
use function str_starts_with;

/**
 * Returns the cosine similarity between two vectors. If the score argument is true, the result
 * is normalized to a value between 0 and 1 for use as a vector search score.
 *
 * New in MongoDB 8.3
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/similarityCosine/
 * @internal
 */
final class SimilarityCosineOperator implements ResolvesToDouble, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$similarityCosine';
    public const PROPERTIES = ['vectors' => 'vectors', 'score' => 'score'];

    /**
     * @var BSONArray|PackedArray|ResolvesToArray|array|string $vectors An array of exactly two expressions that each resolve to an array of numbers.
     * Both arrays must have the same length.
     */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $vectors;

    /** @var Optional|bool $score If true, normalizes the result to a value between 0 and 1 for use as a vector search score. Defaults to false. */
    public readonly Optional|bool $score;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $vectors An array of exactly two expressions that each resolve to an array of numbers.
     * Both arrays must have the same length.
     * @param Optional|bool $score If true, normalizes the result to a value between 0 and 1 for use as a vector search score. Defaults to false.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $vectors,
        Optional|bool $score = Optional::Undefined,
    ) {
        if (is_string($vectors) && ! str_starts_with($vectors, '$')) {
            throw new InvalidArgumentException('Argument $vectors can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($vectors) && ! array_is_list($vectors)) {
            throw new InvalidArgumentException('Expected $vectors argument to be a list, got an associative array.');
        }

        /** @psalm-suppress RedundantCondition */
        if (is_countable($vectors) && count($vectors) !== 2) {
            throw new InvalidArgumentException(sprintf('Expected exactly %d items for $vectors, got %d.', 2, count($vectors)));
        }

        $this->vectors = $vectors;
        $this->score = $score;
    }
}
