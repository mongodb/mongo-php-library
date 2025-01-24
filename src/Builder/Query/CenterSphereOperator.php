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
 * Specifies a circle using either legacy coordinate pairs or GeoJSON format for $geoWithin queries when using spherical geometry. The 2dsphere and 2d indexes support $centerSphere.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/centerSphere/
 * @internal
 */
final class CenterSphereOperator implements GeometryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$centerSphere';
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
