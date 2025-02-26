<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use stdClass;

use function is_array;

/**
 * The $elemMatch operator matches documents that contain an array field with at least one element that matches all the specified query criteria.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/
 * @internal
 */
final class ElemMatchOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$elemMatch';
    public const PROPERTIES = ['query' => 'query'];

    /** @var DateTimeInterface|FieldQueryInterface|QueryInterface|Type|array|bool|float|int|null|stdClass|string $query */
    public readonly DateTimeInterface|Type|FieldQueryInterface|QueryInterface|stdClass|array|bool|float|int|null|string $query;

    /**
     * @param DateTimeInterface|FieldQueryInterface|QueryInterface|Type|array|bool|float|int|null|stdClass|string $query
     */
    public function __construct(
        DateTimeInterface|Type|FieldQueryInterface|QueryInterface|stdClass|array|bool|float|int|null|string $query,
    ) {
        if (is_array($query)) {
            $query = QueryObject::create($query);
        }

        $this->query = $query;
    }
}
