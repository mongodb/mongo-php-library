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
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * Converts a document to an array of documents representing key-value pairs.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/
 * @internal
 */
final class ObjectToArrayOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$objectToArray';
    public const PROPERTIES = ['object' => 'object'];

    /** @var Document|ResolvesToObject|Serializable|array|stdClass|string $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields. */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array|string $object;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields.
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array|string $object)
    {
        if (is_string($object) && ! str_starts_with($object, '$')) {
            throw new InvalidArgumentException('Argument $object can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->object = $object;
    }
}
