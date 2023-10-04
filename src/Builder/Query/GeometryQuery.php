<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Model\BSONArray;

class GeometryQuery implements ExpressionInterface
{
    public const NAME = '$geometry';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param non-empty-string $type */
    public string $type;

    /** @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $coordinates */
    public PackedArray|BSONArray|array $coordinates;

    /** @param Document|Serializable|array|object $crs */
    public array|object $crs;

    /**
     * @param non-empty-string $type
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $coordinates
     * @param Document|Serializable|array|object $crs
     */
    public function __construct(string $type, PackedArray|BSONArray|array $coordinates, array|object $crs)
    {
        $this->type = $type;
        if (\is_array($coordinates) && ! \array_is_list($coordinates)) {
            throw new \InvalidArgumentException('Expected $coordinates argument to be a list, got an associative array.');
        }
        $this->coordinates = $coordinates;
        $this->crs = $crs;
    }
}
