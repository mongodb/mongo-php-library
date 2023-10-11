<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;

/**
 * Matches arrays that contain all elements specified in the query.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/all/
 */
class AllOperator implements FieldQueryInterface
{
    public const NAME = '$all';
    public const ENCODE = Encode::Single;

    /** @param list<BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass> ...$value */
    public array $value;

    /**
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass ...$value
     * @no-named-arguments
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string ...$value,
    ) {
        if (\count($value) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $value, got %d.', 1, \count($value)));
        }
        if (! array_is_list($value)) {
            throw new InvalidArgumentException('Expected $value arguments to be a list (array), named arguments are not supported');
        }
        $this->value = $value;
    }
}
