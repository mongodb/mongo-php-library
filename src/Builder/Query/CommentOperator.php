<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Adds a comment to a query predicate.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/comment/
 */
readonly class CommentOperator implements QueryInterface
{
    public const NAME = '$comment';
    public const ENCODE = Encode::Single;

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
