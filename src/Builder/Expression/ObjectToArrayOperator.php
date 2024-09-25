<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Converts a document to an array of documents representing key-value pairs.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/
 */
class ObjectToArrayOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var Document|ResolvesToObject|Serializable|array|stdClass $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields. */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array $object;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields.
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array $object)
    {
        $this->object = $object;
    }

    public function getOperator(): string
    {
        return '$objectToArray';
    }
}
