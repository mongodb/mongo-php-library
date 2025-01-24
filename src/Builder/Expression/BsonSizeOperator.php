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
 * Returns the size in bytes of a given document (i.e. BSON type Object) when encoded as BSON.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/
 * @internal
 */
final class BsonSizeOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$bsonSize';
    public const PROPERTIES = ['object' => 'object'];

    /** @var Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|stdClass|string $object */
    public readonly Document|Serializable|ResolvesToNull|ResolvesToObject|stdClass|array|null|string $object;

    /**
     * @param Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|stdClass|string $object
     */
    public function __construct(
        Document|Serializable|ResolvesToNull|ResolvesToObject|stdClass|array|null|string $object,
    ) {
        if (is_string($object) && ! str_starts_with($object, '$')) {
            throw new InvalidArgumentException('Argument $object can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->object = $object;
    }
}
