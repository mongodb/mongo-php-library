<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Specifies a minimum distance to limit the results of $near and $nearSphere queries. For use with 2dsphere index only.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/minDistance/
 * @internal
 */
final class MinDistanceOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$minDistance';
    public const PROPERTIES = ['value' => 'value'];

    /** @var Int64|float|int $value */
    public readonly Int64|float|int $value;

    /**
     * @param Int64|float|int $value
     */
    public function __construct(Int64|float|int $value)
    {
        $this->value = $value;
    }
}
