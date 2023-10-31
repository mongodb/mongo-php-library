<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;

use function is_array;

/**
 * The $elemMatch operator matches documents that contain an array field with at least one element that matches all the specified query criteria.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/
 */
class ElemMatchOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var QueryInterface|array $query */
    public readonly QueryInterface|array $query;

    /**
     * @param QueryInterface|array $query
     */
    public function __construct(QueryInterface|array $query)
    {
        if (is_array($query)) {
            $query = QueryObject::create($query);
        }

        $this->query = $query;
    }

    public function getOperator(): string
    {
        return '$elemMatch';
    }
}
