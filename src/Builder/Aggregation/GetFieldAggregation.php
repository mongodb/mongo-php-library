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
use MongoDB\Builder\Expression\ResolvesToAny;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Returns the value of a specified field from a document. You can use $getField to retrieve the value of fields with names that contain periods (.) or start with dollar signs ($).
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/
 */
class GetFieldAggregation implements ResolvesToAny
{
    public const NAME = '$getField';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param non-empty-string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     */
    public string $field;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|Optional|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|Optional|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $input;

    /**
     * @param non-empty-string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|Optional|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public function __construct(
        string $field,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|Optional|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $input = Optional::Undefined,
    ) {
        $this->field = $field;
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
    }
}
