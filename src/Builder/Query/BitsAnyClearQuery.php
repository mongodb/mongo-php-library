<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Model\BSONArray;

class BitsAnyClearQuery implements QueryInterface
{
    public const NAME = '$bitsAnyClear';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|Binary|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $bitmask */
    public Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask;

    /**
     * @param BSONArray|Binary|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $bitmask
     */
    public function __construct(Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask)
    {
        if (\is_array($bitmask) && ! \array_is_list($bitmask)) {
            throw new \InvalidArgumentException('Expected $bitmask argument to be a list, got an associative array.');
        }
        $this->bitmask = $bitmask;
    }
}
