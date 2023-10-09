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
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Defines variables for use within the scope of a subexpression and returns the result of the subexpression. Accepts named parameters.
 * Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/let/
 */
class LetAggregation implements ResolvesToAny
{
    public const NAME = '$let';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /**
     * @param Document|Serializable|array|stdClass $vars Assignment block for the variables accessible in the in expression. To assign a variable, specify a string for the variable name and assign a valid expression for the value.
     * The variable assignments have no meaning outside the in expression, not even within the vars block itself.
     */
    public Document|Serializable|stdClass|array $vars;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $in The expression to evaluate. */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $in;

    /**
     * @param Document|Serializable|array|stdClass $vars Assignment block for the variables accessible in the in expression. To assign a variable, specify a string for the variable name and assign a valid expression for the value.
     * The variable assignments have no meaning outside the in expression, not even within the vars block itself.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $in The expression to evaluate.
     */
    public function __construct(
        Document|Serializable|stdClass|array $vars,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $in,
    ) {
        $this->vars = $vars;
        if (\is_array($in) && ! \array_is_list($in)) {
            throw new \InvalidArgumentException('Expected $in argument to be a list, got an associative array.');
        }

        $this->in = $in;
    }
}
