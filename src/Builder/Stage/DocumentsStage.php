<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Model\BSONArray;

class DocumentsStage implements StageInterface
{
    public const NAME = '$documents';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $documents $documents accepts any valid expression that resolves to an array of objects. This includes:
     * - system variables, such as $$NOW or $$SEARCH_META
     * - $let expressions
     * - variables in scope from $lookup expressions
     * Expressions that do not resolve to a current document, like $myField or $$ROOT, will result in an error.
     */
    public PackedArray|ResolvesToArray|BSONArray|array $documents;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $documents $documents accepts any valid expression that resolves to an array of objects. This includes:
     * - system variables, such as $$NOW or $$SEARCH_META
     * - $let expressions
     * - variables in scope from $lookup expressions
     * Expressions that do not resolve to a current document, like $myField or $$ROOT, will result in an error.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $documents)
    {
        if (\is_array($documents) && ! \array_is_list($documents)) {
            throw new \InvalidArgumentException('Expected $documents argument to be a list, got an associative array.');
        }
        $this->documents = $documents;
    }
}
