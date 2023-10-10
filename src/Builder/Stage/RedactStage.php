<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

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
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Reshapes each document in the stream by restricting the content for each document based on information stored in the documents themselves. Incorporates the functionality of $project and $match. Can be used to implement field level redaction. For each input document, outputs either one or zero documents.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/
 */
class RedactStage implements StageInterface
{
    public const NAME = '$redact';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ) {
        $this->expression = $expression;
    }
}
