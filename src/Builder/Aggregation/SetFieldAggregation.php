<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

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
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Adds, updates, or removes a specified field in a document. You can use $setField to add, update, or remove fields with names that contain periods (.) or start with dollar signs ($).
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/
 */
class SetFieldAggregation implements ResolvesToObject
{
    public const NAME = '$setField';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ResolvesToString|non-empty-string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant. */
    public ResolvesToString|string $field;

    /** @param Document|ResolvesToObject|Serializable|array|stdClass $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined. */
    public Document|Serializable|ResolvesToObject|stdClass|array $input;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value The value that you want to assign to field. value can be any valid expression.
     * Set to $$REMOVE to remove field from the input document.
     */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param ResolvesToString|non-empty-string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant.
     * @param Document|ResolvesToObject|Serializable|array|stdClass $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value The value that you want to assign to field. value can be any valid expression.
     * Set to $$REMOVE to remove field from the input document.
     */
    public function __construct(
        ResolvesToString|string $field,
        Document|Serializable|ResolvesToObject|stdClass|array $input,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ) {
        $this->field = $field;
        $this->input = $input;
        if (\is_array($value) && ! \array_is_list($value)) {
            throw new \InvalidArgumentException('Expected $value argument to be a list, got an associative array.');
        }

        $this->value = $value;
    }
}
