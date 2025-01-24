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
 * The moreLikeThis operator returns documents similar to input documents.
 * The moreLikeThis operator allows you to build features for your applications
 * that display similar or alternative results based on one or more given documents.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/moreLikeThis/
 * @internal
 */
final class MoreLikeThisOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'moreLikeThis';
    public const PROPERTIES = ['like' => 'like', 'score' => 'score'];

    /** @var BSONArray|Document|PackedArray|Serializable|array|stdClass $like */
    public readonly Document|PackedArray|Serializable|BSONArray|stdClass|array $like;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param BSONArray|Document|PackedArray|Serializable|array|stdClass $like
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        Document|PackedArray|Serializable|BSONArray|stdClass|array $like,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->like = $like;
        $this->score = $score;
    }
}
