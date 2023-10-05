<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Adds a comment to a query predicate.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/comment/
 */
class CommentQuery implements ExpressionInterface
{
    public const NAME = '$comment';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param non-empty-string $comment */
    public string $comment;

    /**
     * @param non-empty-string $comment
     */
    public function __construct(string $comment)
    {
        $this->comment = $comment;
    }
}
