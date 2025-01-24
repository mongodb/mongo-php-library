<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * The compound operator combines two or more operators into a single query.
 * Each element of a compound query is called a clause, and each clause
 * consists of one or more sub-queries.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/compound/
 * @internal
 */
final class CompoundOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'compound';

    public const PROPERTIES = [
        'must' => 'must',
        'mustNot' => 'mustNot',
        'should' => 'should',
        'filter' => 'filter',
        'minimumShouldMatch' => 'minimumShouldMatch',
        'score' => 'score',
    ];

    /** @var Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $must */
    public readonly Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $must;

    /** @var Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $mustNot */
    public readonly Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $mustNot;

    /** @var Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $should */
    public readonly Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $should;

    /** @var Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $filter */
    public readonly Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $filter;

    /** @var Optional|int $minimumShouldMatch */
    public readonly Optional|int $minimumShouldMatch;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $must
     * @param Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $mustNot
     * @param Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $should
     * @param Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $filter
     * @param Optional|int $minimumShouldMatch
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $must = Optional::Undefined,
        Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $mustNot = Optional::Undefined,
        Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $should = Optional::Undefined,
        Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $filter = Optional::Undefined,
        Optional|int $minimumShouldMatch = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->must = $must;
        $this->mustNot = $mustNot;
        $this->should = $should;
        $this->filter = $filter;
        $this->minimumShouldMatch = $minimumShouldMatch;
        $this->score = $score;
    }
}
