<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use stdClass;

/**
 * Converts a document to an array of documents representing key-value pairs.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/
 */
class ObjectToArrayOperator implements ResolvesToArray
{
    public const NAME = '$objectToArray';
    public const ENCODE = Encode::Single;

    /** @param Document|ResolvesToObject|Serializable|array|stdClass $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields. */
    public Document|Serializable|ResolvesToObject|stdClass|array $object;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields.
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array $object)
    {
        $this->object = $object;
    }
}
