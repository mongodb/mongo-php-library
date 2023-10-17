<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Adds a comment to a query predicate.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/comment/
 */
class CommentOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var non-empty-string $comment */
    public readonly string $comment;

    /**
     * @param non-empty-string $comment
     */
    public function __construct(string $comment)
    {
        $this->comment = $comment;
    }

    public function getOperator(): string
    {
        return '$comment';
    }
}
