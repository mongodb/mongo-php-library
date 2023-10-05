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
use stdClass;

class BsonSizeAggregation implements ResolvesToInt
{
    public const NAME = '$bsonSize';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|stdClass $object */
    public Document|Serializable|ResolvesToNull|ResolvesToObject|stdClass|array|null $object;

    /**
     * @param Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|stdClass $object
     */
    public function __construct(Document|Serializable|ResolvesToNull|ResolvesToObject|stdClass|array|null $object)
    {
        $this->object = $object;
    }
}
