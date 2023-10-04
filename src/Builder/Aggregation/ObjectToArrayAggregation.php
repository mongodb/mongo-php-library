<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToObject;

class ObjectToArrayAggregation implements ResolvesToArray
{
    public const NAME = '$objectToArray';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|ResolvesToObject|Serializable|array|object $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields. */
    public array|object $object;

    /**
     * @param Document|ResolvesToObject|Serializable|array|object $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields.
     */
    public function __construct(array|object $object)
    {
        $this->object = $object;
    }
}
