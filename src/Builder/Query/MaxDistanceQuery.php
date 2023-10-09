<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDecimal;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Specifies a maximum distance to limit the results of $near and $nearSphere queries. The 2dsphere and 2d indexes support $maxDistance.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/maxDistance/
 */
class MaxDistanceQuery implements QueryInterface
{
    public const NAME = '$maxDistance';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $value */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $value;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $value
     */
    public function __construct(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $value,
    ) {
        $this->value = $value;
    }
}
