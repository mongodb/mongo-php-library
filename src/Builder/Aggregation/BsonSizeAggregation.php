<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToNull;
use MongoDB\Builder\Expression\ResolvesToObject;

class BsonSizeAggregation implements ResolvesToInt
{
    public const NAME = '$bsonSize';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|object $object */
    public array|null|object $object;

    /**
     * @param Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|object $object
     */
    public function __construct(array|null|object $object)
    {
        $this->object = $object;
    }
}
