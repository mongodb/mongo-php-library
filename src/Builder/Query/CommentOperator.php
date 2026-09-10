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
 * @internal
 */
final class CommentOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$comment';
    public const PROPERTIES = ['comment' => 'comment'];

    /** @var string $comment */
    public readonly string $comment;

    /** @param string $comment */
    public function __construct(string $comment)
    {
        $this->comment = $comment;
    }
}
