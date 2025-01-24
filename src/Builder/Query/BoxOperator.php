<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Specifies a rectangular box using legacy coordinate pairs for $geoWithin queries. The 2d index supports $box.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/box/
 * @internal
 */
final class BoxOperator implements GeometryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$box';
    public const PROPERTIES = ['value' => 'value'];

    /** @var BSONArray|PackedArray|array $value */
    public readonly PackedArray|BSONArray|array $value;

    /**
     * @param BSONArray|PackedArray|array $value
     */
    public function __construct(PackedArray|BSONArray|array $value)
    {
        if (is_array($value) && ! array_is_list($value)) {
            throw new InvalidArgumentException('Expected $value argument to be a list, got an associative array.');
        }

        $this->value = $value;
    }
}
