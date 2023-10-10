<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

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
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Evaluates a series of case expressions. When it finds an expression which evaluates to true, $switch executes a specified expression and breaks out of the control flow.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/
 */
class SwitchOperator implements ResolvesToAny
{
    public const NAME = '$switch';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param BSONArray|PackedArray|array $branches An array of control branch documents. Each branch is a document with the following fields:
     * - case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * - then Can be any valid expression.
     * The branches array must contain at least one branch document.
     */
    public PackedArray|BSONArray|array $branches;

    /**
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $default The path to take if no branch case expression evaluates to true.
     * Although optional, if default is unspecified and no branch case evaluates to true, $switch returns an error.
     */
    public Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $default;

    /**
     * @param BSONArray|PackedArray|array $branches An array of control branch documents. Each branch is a document with the following fields:
     * - case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * - then Can be any valid expression.
     * The branches array must contain at least one branch document.
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $default The path to take if no branch case expression evaluates to true.
     * Although optional, if default is unspecified and no branch case evaluates to true, $switch returns an error.
     */
    public function __construct(
        PackedArray|BSONArray|array $branches,
        Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $default = Optional::Undefined,
    ) {
        if (\is_array($branches) && ! \array_is_list($branches)) {
            throw new \InvalidArgumentException('Expected $branches argument to be a list, got an associative array.');
        }

        $this->branches = $branches;
        $this->default = $default;
    }
}
