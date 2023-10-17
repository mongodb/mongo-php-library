<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Projection;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\ProjectionInterface;

/**
 * Limits the number of elements projected from an array. Supports skip and limit slices.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/projection/slice/
 */
class SliceOperator implements ProjectionInterface, OperatorInterface
{
    public const ENCODE = Encode::Array;

    /** @var int $limit */
    public readonly int $limit;

    /** @var int $skip */
    public readonly int $skip;

    /**
     * @param int $limit
     * @param int $skip
     */
    public function __construct(int $limit, int $skip)
    {
        $this->limit = $limit;
        $this->skip = $skip;
    }

    public function getOperator(): string
    {
        return '$slice';
    }
}
