<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Projection;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\ProjectionInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;

use function is_array;

/**
 * Projects the first element in an array that matches the specified $elemMatch condition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/projection/elemMatch/
 */
class ElemMatchOperator implements ProjectionInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

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
