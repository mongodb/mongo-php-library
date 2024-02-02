<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Javascript;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\Type;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * @internal
 */
trait FactoryTrait
{
    /**
     * Returns the absolute value of a number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/abs/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $value
     */
    public static function abs(Decimal128|Int64|ResolvesToNumber|float|int $value): AbsOperator
    {
        return new AbsOperator($value);
    }

    /**
     * Returns the inverse cosine (arc cosine) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acos/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $acos takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $acos returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acos returns values as a double. $acos can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function acos(Decimal128|Int64|ResolvesToNumber|float|int $expression): AcosOperator
    {
        return new AcosOperator($expression);
    }

    /**
     * Returns the inverse hyperbolic cosine (hyperbolic arc cosine) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acosh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $acosh takes any valid expression that resolves to a number between 1 and +Infinity, e.g. 1 <= value <= +Infinity.
     * $acosh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acosh returns values as a double. $acosh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function acosh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AcoshOperator
    {
        return new AcoshOperator($expression);
    }

    /**
     * Adds numbers to return the sum, or adds numbers and a date to return a new date. If adding numbers and a date, treats the numbers as milliseconds. Accepts any number of argument expressions, but at most, one expression can resolve to a date.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/add/
     * @no-named-arguments
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int ...$expression The arguments can be any valid expression as long as they resolve to either all numbers or to numbers and a date.
     */
    public static function add(
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int ...$expression,
    ): AddOperator
    {
        return new AddOperator(...$expression);
    }

    /**
     * Returns true if no element of a set evaluates to false, otherwise, returns false. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/allElementsTrue/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression
     */
    public static function allElementsTrue(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
    ): AllElementsTrueOperator
    {
        return new AllElementsTrueOperator($expression);
    }

    /**
     * Returns true only when all its expressions evaluate to true. Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/and/
     * @no-named-arguments
     * @param Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|Type|array|bool|float|int|null|stdClass|string ...$expression
     */
    public static function and(
        Decimal128|Int64|Type|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ): AndOperator
    {
        return new AndOperator(...$expression);
    }

    /**
     * Returns true if any elements of a set evaluate to true; otherwise, returns false. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/anyElementTrue/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression
     */
    public static function anyElementTrue(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
    ): AnyElementTrueOperator
    {
        return new AnyElementTrueOperator($expression);
    }

    /**
     * Returns the element at the specified array index.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayElemAt/
     * @param BSONArray|PackedArray|ResolvesToArray|array $array
     * @param ResolvesToInt|int $idx
     */
    public static function arrayElemAt(
        PackedArray|ResolvesToArray|BSONArray|array $array,
        ResolvesToInt|int $idx,
    ): ArrayElemAtOperator
    {
        return new ArrayElemAtOperator($array, $idx);
    }

    /**
     * Converts an array of key value pairs to a document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/
     * @param BSONArray|PackedArray|ResolvesToArray|array $array
     */
    public static function arrayToObject(PackedArray|ResolvesToArray|BSONArray|array $array): ArrayToObjectOperator
    {
        return new ArrayToObjectOperator($array);
    }

    /**
     * Returns the inverse sin (arc sine) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asin/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $asin takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $asin returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asin returns values as a double. $asin can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function asin(Decimal128|Int64|ResolvesToNumber|float|int $expression): AsinOperator
    {
        return new AsinOperator($expression);
    }

    /**
     * Returns the inverse hyperbolic sine (hyperbolic arc sine) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asinh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function asinh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AsinhOperator
    {
        return new AsinhOperator($expression);
    }

    /**
     * Returns the inverse tangent (arc tangent) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atan/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $atan takes any valid expression that resolves to a number.
     * $atan returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atan returns values as a double. $atan can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function atan(Decimal128|Int64|ResolvesToNumber|float|int $expression): AtanOperator
    {
        return new AtanOperator($expression);
    }

    /**
     * Returns the inverse tangent (arc tangent) of y / x in radians, where y and x are the first and second values passed to the expression respectively.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atan2/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $y $atan2 takes any valid expression that resolves to a number.
     * $atan2 returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atan returns values as a double. $atan2 can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     * @param Decimal128|Int64|ResolvesToNumber|float|int $x
     */
    public static function atan2(
        Decimal128|Int64|ResolvesToNumber|float|int $y,
        Decimal128|Int64|ResolvesToNumber|float|int $x,
    ): Atan2Operator
    {
        return new Atan2Operator($y, $x);
    }

    /**
     * Returns the inverse hyperbolic tangent (hyperbolic arc tangent) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atanh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $atanh takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $atanh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atanh returns values as a double. $atanh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function atanh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AtanhOperator
    {
        return new AtanhOperator($expression);
    }

    /**
     * Returns an average of numerical values. Ignores non-numeric values.
     * Changed in MongoDB 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/
     * @no-named-arguments
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function avg(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): AvgOperator
    {
        return new AvgOperator(...$expression);
    }

    /**
     * Returns the size of a given string or binary data value's content in bytes.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/binarySize/
     * @param Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression
     */
    public static function binarySize(
        Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression,
    ): BinarySizeOperator
    {
        return new BinarySizeOperator($expression);
    }

    /**
     * Returns the result of a bitwise and operation on an array of int or long values.
     * New in MongoDB 6.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitAnd/
     * @no-named-arguments
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public static function bitAnd(Int64|ResolvesToInt|ResolvesToLong|int ...$expression): BitAndOperator
    {
        return new BitAndOperator(...$expression);
    }

    /**
     * Returns the result of a bitwise not operation on a single argument or an array that contains a single int or long value.
     * New in MongoDB 6.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitNot/
     * @param Int64|ResolvesToInt|ResolvesToLong|int $expression
     */
    public static function bitNot(Int64|ResolvesToInt|ResolvesToLong|int $expression): BitNotOperator
    {
        return new BitNotOperator($expression);
    }

    /**
     * Returns the result of a bitwise or operation on an array of int or long values.
     * New in MongoDB 6.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitOr/
     * @no-named-arguments
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public static function bitOr(Int64|ResolvesToInt|ResolvesToLong|int ...$expression): BitOrOperator
    {
        return new BitOrOperator(...$expression);
    }

    /**
     * Returns the result of a bitwise xor (exclusive or) operation on an array of int and long values.
     * New in MongoDB 6.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitXor/
     * @no-named-arguments
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public static function bitXor(Int64|ResolvesToInt|ResolvesToLong|int ...$expression): BitXorOperator
    {
        return new BitXorOperator(...$expression);
    }

    /**
     * Returns the size in bytes of a given document (i.e. BSON type Object) when encoded as BSON.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/
     * @param Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|stdClass $object
     */
    public static function bsonSize(
        Document|Serializable|ResolvesToNull|ResolvesToObject|stdClass|array|null $object,
    ): BsonSizeOperator
    {
        return new BsonSizeOperator($object);
    }

    /**
     * Represents a single case in a $switch expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/
     * @param ResolvesToBool|bool $case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $then Can be any valid expression.
     */
    public static function case(
        ResolvesToBool|bool $case,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then,
    ): CaseOperator
    {
        return new CaseOperator($case, $then);
    }

    /**
     * Returns the smallest integer greater than or equal to the specified number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ceil/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression If the argument resolves to a value of null or refers to a field that is missing, $ceil returns null. If the argument resolves to NaN, $ceil returns NaN.
     */
    public static function ceil(Decimal128|Int64|ResolvesToNumber|float|int $expression): CeilOperator
    {
        return new CeilOperator($expression);
    }

    /**
     * Returns 0 if the two values are equivalent, 1 if the first value is greater than the second, and -1 if the first value is less than the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cmp/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2
     */
    public static function cmp(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ): CmpOperator
    {
        return new CmpOperator($expression1, $expression2);
    }

    /**
     * Concatenates any number of strings.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concat/
     * @no-named-arguments
     * @param ResolvesToString|string ...$expression
     */
    public static function concat(ResolvesToString|string ...$expression): ConcatOperator
    {
        return new ConcatOperator(...$expression);
    }

    /**
     * Concatenates arrays to return the concatenated array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concatArrays/
     * @no-named-arguments
     * @param BSONArray|PackedArray|ResolvesToArray|array ...$array
     */
    public static function concatArrays(PackedArray|ResolvesToArray|BSONArray|array ...$array): ConcatArraysOperator
    {
        return new ConcatArraysOperator(...$array);
    }

    /**
     * A ternary operator that evaluates one expression, and depending on the result, returns the value of one of the other two expressions. Accepts either three expressions in an ordered list or three named parameters.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cond/
     * @param ResolvesToBool|bool $if
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $then
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $else
     */
    public static function cond(
        ResolvesToBool|bool $if,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $else,
    ): CondOperator
    {
        return new CondOperator($if, $then, $else);
    }

    /**
     * Converts a value to a specified type.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input
     * @param ResolvesToInt|ResolvesToString|int|string $to
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public static function convert(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input,
        ResolvesToInt|ResolvesToString|int|string $to,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ): ConvertOperator
    {
        return new ConvertOperator($input, $to, $onError, $onNull);
    }

    /**
     * Returns the cosine of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cos/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $cos takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $cos returns values as a double. $cos can also return values as a 128-bit decimal as long as the <expression> resolves to a 128-bit decimal value.
     */
    public static function cos(Decimal128|Int64|ResolvesToNumber|float|int $expression): CosOperator
    {
        return new CosOperator($expression);
    }

    /**
     * Returns the hyperbolic cosine of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cosh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $cosh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $cosh returns values as a double. $cosh can also return values as a 128-bit decimal if the <expression> resolves to a 128-bit decimal value.
     */
    public static function cosh(Decimal128|Int64|ResolvesToNumber|float|int $expression): CoshOperator
    {
        return new CoshOperator($expression);
    }

    /**
     * Adds a number of time units to a date object.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|string $unit The unit used to measure the amount of time added to the startDate.
     * @param Int64|ResolvesToInt|ResolvesToLong|int $amount
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dateAdd(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ResolvesToString|string $unit,
        Int64|ResolvesToInt|ResolvesToLong|int $amount,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): DateAddOperator
    {
        return new DateAddOperator($startDate, $unit, $amount, $timezone);
    }

    /**
     * Returns the difference between two dates.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $startDate The start of the time period. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $endDate The end of the time period. The endDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|string $unit The time measurement unit between the startDate and endDate
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|ResolvesToString|string $startOfWeek Used when the unit is equal to week. Defaults to Sunday. The startOfWeek parameter is an expression that resolves to a case insensitive string
     */
    public static function dateDiff(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $endDate,
        ResolvesToString|string $unit,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|ResolvesToString|string $startOfWeek = Optional::Undefined,
    ): DateDiffOperator
    {
        return new DateDiffOperator($startDate, $endDate, $unit, $timezone, $startOfWeek);
    }

    /**
     * Constructs a BSON Date object given the date's constituent parts.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromParts/
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $year Calendar year. Can be any expression that evaluates to a number.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $month Month. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeek Week of year. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $day Day of month. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $hour Hour. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $minute Minute. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $second Second. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $millisecond Millisecond. Defaults to 0.
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dateFromParts(
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $year = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeekYear = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $month = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeek = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $day = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoDayOfWeek = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $hour = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $minute = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $second = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $millisecond = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): DateFromPartsOperator
    {
        return new DateFromPartsOperator($year, $isoWeekYear, $month, $isoWeek, $day, $isoDayOfWeek, $hour, $minute, $second, $millisecond, $timezone);
    }

    /**
     * Converts a date/time string to a date object.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/
     * @param ResolvesToString|string $dateString The date/time string to convert to a date object.
     * @param Optional|ResolvesToString|string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     * @param Optional|ResolvesToString|string $timezone The time zone to use to format the date.
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError If $dateFromString encounters an error while parsing the given dateString, it outputs the result value of the provided onError expression. This result value can be of any type.
     * If you do not specify onError, $dateFromString throws an error if it cannot parse dateString.
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onNull If the dateString provided to $dateFromString is null or missing, it outputs the result value of the provided onNull expression. This result value can be of any type.
     * If you do not specify onNull and dateString is null or missing, then $dateFromString outputs null.
     */
    public static function dateFromString(
        ResolvesToString|string $dateString,
        Optional|ResolvesToString|string $format = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ): DateFromStringOperator
    {
        return new DateFromStringOperator($dateString, $format, $timezone, $onError, $onNull);
    }

    /**
     * Subtracts a number of time units from a date object.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|string $unit The unit used to measure the amount of time added to the startDate.
     * @param Int64|ResolvesToInt|ResolvesToLong|int $amount
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dateSubtract(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ResolvesToString|string $unit,
        Int64|ResolvesToInt|ResolvesToLong|int $amount,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): DateSubtractOperator
    {
        return new DateSubtractOperator($startDate, $unit, $amount, $timezone);
    }

    /**
     * Returns a document containing the constituent parts of a date.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToParts/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The input date for which to return parts. date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|bool $iso8601 If set to true, modifies the output document to use ISO week date fields. Defaults to false.
     */
    public static function dateToParts(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|bool $iso8601 = Optional::Undefined,
    ): DateToPartsOperator
    {
        return new DateToPartsOperator($date, $timezone, $iso8601);
    }

    /**
     * Returns the date as a formatted string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToString/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to convert to string. Must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     * @param Optional|ResolvesToString|string $timezone The time zone to use to format the date.
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onNull The value to return if the date is null or missing.
     * If unspecified, $dateToString returns null if the date is null or missing.
     */
    public static function dateToString(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $format = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ): DateToStringOperator
    {
        return new DateToStringOperator($date, $format, $timezone, $onNull);
    }

    /**
     * Truncates a date.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateTrunc/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to truncate, specified in UTC. The date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|string $unit The unit of time, specified as an expression that must resolve to one of these strings: year, quarter, week, month, day, hour, minute, second.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $binSize The numeric time value, specified as an expression that must resolve to a positive non-zero number. Defaults to 1.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|string $startOfWeek The start of the week. Used when
     * unit is week. Defaults to Sunday.
     */
    public static function dateTrunc(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|string $unit,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $binSize = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|string $startOfWeek = Optional::Undefined,
    ): DateTruncOperator
    {
        return new DateTruncOperator($date, $unit, $binSize, $timezone, $startOfWeek);
    }

    /**
     * Returns the day of the month for a date as a number between 1 and 31.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfMonth/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfMonth(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): DayOfMonthOperator
    {
        return new DayOfMonthOperator($date, $timezone);
    }

    /**
     * Returns the day of the week for a date as a number between 1 (Sunday) and 7 (Saturday).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfWeek/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfWeek(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): DayOfWeekOperator
    {
        return new DayOfWeekOperator($date, $timezone);
    }

    /**
     * Returns the day of the year for a date as a number between 1 and 366 (leap year).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfYear/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfYear(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): DayOfYearOperator
    {
        return new DayOfYearOperator($date, $timezone);
    }

    /**
     * Converts a value from degrees to radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/degreesToRadians/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $degreesToRadians takes any valid expression that resolves to a number.
     * By default $degreesToRadians returns values as a double. $degreesToRadians can also return values as a 128-bit decimal as long as the <expression> resolves to a 128-bit decimal value.
     */
    public static function degreesToRadians(
        Decimal128|Int64|ResolvesToNumber|float|int $expression,
    ): DegreesToRadiansOperator
    {
        return new DegreesToRadiansOperator($expression);
    }

    /**
     * Returns the result of dividing the first number by the second. Accepts two argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/divide/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $dividend The first argument is the dividend, and the second argument is the divisor; i.e. the first argument is divided by the second argument.
     * @param Decimal128|Int64|ResolvesToNumber|float|int $divisor
     */
    public static function divide(
        Decimal128|Int64|ResolvesToNumber|float|int $dividend,
        Decimal128|Int64|ResolvesToNumber|float|int $divisor,
    ): DivideOperator
    {
        return new DivideOperator($dividend, $divisor);
    }

    /**
     * Returns true if the values are equivalent.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/eq/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2
     */
    public static function eq(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ): EqOperator
    {
        return new EqOperator($expression1, $expression2);
    }

    /**
     * Raises e to the specified exponent.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/exp/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $exponent
     */
    public static function exp(Decimal128|Int64|ResolvesToNumber|float|int $exponent): ExpOperator
    {
        return new ExpOperator($exponent);
    }

    /**
     * Selects a subset of the array to return an array with only the elements that match the filter condition.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input
     * @param ResolvesToBool|bool $cond An expression that resolves to a boolean value used to determine if an element should be included in the output array. The expression references each element of the input array individually with the variable name specified in as.
     * @param Optional|string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     * @param Optional|ResolvesToInt|int $limit A number expression that restricts the number of matching array elements that $filter returns. You cannot specify a limit less than 1. The matching array elements are returned in the order they appear in the input array.
     * If the specified limit is greater than the number of matching array elements, $filter returns all matching array elements. If the limit is null, $filter returns all matching array elements.
     */
    public static function filter(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToBool|bool $cond,
        Optional|string $as = Optional::Undefined,
        Optional|ResolvesToInt|int $limit = Optional::Undefined,
    ): FilterOperator
    {
        return new FilterOperator($input, $cond, $as, $limit);
    }

    /**
     * Returns the result of an expression for the first document in an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/first/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression
     */
    public static function first(PackedArray|ResolvesToArray|BSONArray|array $expression): FirstOperator
    {
        return new FirstOperator($expression);
    }

    /**
     * Returns a specified number of elements from the beginning of an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN-array-element/
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return n elements.
     */
    public static function firstN(
        ResolvesToInt|int $n,
        PackedArray|ResolvesToArray|BSONArray|array $input,
    ): FirstNOperator
    {
        return new FirstNOperator($n, $input);
    }

    /**
     * Returns the largest integer less than or equal to the specified number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/floor/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public static function floor(Decimal128|Int64|ResolvesToNumber|float|int $expression): FloorOperator
    {
        return new FloorOperator($expression);
    }

    /**
     * Defines a custom function.
     * New in MongoDB 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/function/
     * @param Javascript|string $body The function definition. You can specify the function definition as either BSON\JavaScript or string.
     * function(arg1, arg2, ...) { ... }
     * @param BSONArray|PackedArray|array $args Arguments passed to the function body. If the body function does not take an argument, you can specify an empty array [ ].
     * @param string $lang
     */
    public static function function(
        Javascript|string $body,
        PackedArray|BSONArray|array $args = [],
        string $lang = 'js',
    ): FunctionOperator
    {
        return new FunctionOperator($body, $args, $lang);
    }

    /**
     * Returns the value of a specified field from a document. You can use $getField to retrieve the value of fields with names that contain periods (.) or start with dollar signs ($).
     * New in MongoDB 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/
     * @param ResolvesToString|string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public static function getField(
        ResolvesToString|string $field,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input = Optional::Undefined,
    ): GetFieldOperator
    {
        return new GetFieldOperator($field, $input);
    }

    /**
     * Returns true if the first value is greater than the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gt/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2
     */
    public static function gt(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ): GtOperator
    {
        return new GtOperator($expression1, $expression2);
    }

    /**
     * Returns true if the first value is greater than or equal to the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gte/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2
     */
    public static function gte(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ): GteOperator
    {
        return new GteOperator($expression1, $expression2);
    }

    /**
     * Returns the hour for a date as a number between 0 and 23.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hour/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function hour(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): HourOperator
    {
        return new HourOperator($date, $timezone);
    }

    /**
     * Returns either the non-null result of the first expression or the result of the second expression if the first expression results in a null result. Null result encompasses instances of undefined values or missing fields. Accepts two expressions as arguments. The result of the second expression can be null.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ifNull/
     * @no-named-arguments
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$expression
     */
    public static function ifNull(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ): IfNullOperator
    {
        return new IfNullOperator(...$expression);
    }

    /**
     * Returns a boolean indicating whether a specified value is in an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/in/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression Any valid expression expression.
     * @param BSONArray|PackedArray|ResolvesToArray|array $array Any valid expression that resolves to an array.
     */
    public static function in(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
        PackedArray|ResolvesToArray|BSONArray|array $array,
    ): InOperator
    {
        return new InOperator($expression, $array);
    }

    /**
     * Searches an array for an occurrence of a specified value and returns the array index of the first occurrence. Array indexes start at zero.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfArray/
     * @param BSONArray|PackedArray|ResolvesToArray|array $array Can be any valid expression as long as it resolves to an array.
     * If the array expression resolves to a value of null or refers to a field that is missing, $indexOfArray returns null.
     * If the array expression does not resolve to an array or null nor refers to a missing field, $indexOfArray returns an error.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $search
     * @param Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public static function indexOfArray(
        PackedArray|ResolvesToArray|BSONArray|array $array,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $search,
        Optional|ResolvesToInt|int $start = Optional::Undefined,
        Optional|ResolvesToInt|int $end = Optional::Undefined,
    ): IndexOfArrayOperator
    {
        return new IndexOfArrayOperator($array, $search, $start, $end);
    }

    /**
     * Searches a string for an occurrence of a substring and returns the UTF-8 byte index of the first occurrence. If the substring is not found, returns -1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfBytes/
     * @param ResolvesToString|string $string Can be any valid expression as long as it resolves to a string.
     * If the string expression resolves to a value of null or refers to a field that is missing, $indexOfBytes returns null.
     * If the string expression does not resolve to a string or null nor refers to a missing field, $indexOfBytes returns an error.
     * @param ResolvesToString|string $substring Can be any valid expression as long as it resolves to a string.
     * @param Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public static function indexOfBytes(
        ResolvesToString|string $string,
        ResolvesToString|string $substring,
        Optional|ResolvesToInt|int $start = Optional::Undefined,
        Optional|ResolvesToInt|int $end = Optional::Undefined,
    ): IndexOfBytesOperator
    {
        return new IndexOfBytesOperator($string, $substring, $start, $end);
    }

    /**
     * Searches a string for an occurrence of a substring and returns the UTF-8 code point index of the first occurrence. If the substring is not found, returns -1
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfCP/
     * @param ResolvesToString|string $string Can be any valid expression as long as it resolves to a string.
     * If the string expression resolves to a value of null or refers to a field that is missing, $indexOfCP returns null.
     * If the string expression does not resolve to a string or null nor refers to a missing field, $indexOfCP returns an error.
     * @param ResolvesToString|string $substring Can be any valid expression as long as it resolves to a string.
     * @param Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public static function indexOfCP(
        ResolvesToString|string $string,
        ResolvesToString|string $substring,
        Optional|ResolvesToInt|int $start = Optional::Undefined,
        Optional|ResolvesToInt|int $end = Optional::Undefined,
    ): IndexOfCPOperator
    {
        return new IndexOfCPOperator($string, $substring, $start, $end);
    }

    /**
     * Determines if the operand is an array. Returns a boolean.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isArray/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function isArray(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): IsArrayOperator
    {
        return new IsArrayOperator($expression);
    }

    /**
     * Returns boolean true if the specified expression resolves to an integer, decimal, double, or long.
     * Returns boolean false if the expression resolves to any other BSON type, null, or a missing field.
     * New in MongoDB 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isNumber/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function isNumber(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): IsNumberOperator
    {
        return new IsNumberOperator($expression);
    }

    /**
     * Returns the weekday number in ISO 8601 format, ranging from 1 (for Monday) to 7 (for Sunday).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoDayOfWeek/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoDayOfWeek(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): IsoDayOfWeekOperator
    {
        return new IsoDayOfWeekOperator($date, $timezone);
    }

    /**
     * Returns the week number in ISO 8601 format, ranging from 1 to 53. Week numbers start at 1 with the week (Monday through Sunday) that contains the year's first Thursday.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoWeek/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoWeek(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): IsoWeekOperator
    {
        return new IsoWeekOperator($date, $timezone);
    }

    /**
     * Returns the year number in ISO 8601 format. The year starts with the Monday of week 1 (ISO 8601) and ends with the Sunday of the last week (ISO 8601).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoWeekYear/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoWeekYear(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): IsoWeekYearOperator
    {
        return new IsoWeekYearOperator($date, $timezone);
    }

    /**
     * Returns the result of an expression for the last document in an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression
     */
    public static function last(PackedArray|ResolvesToArray|BSONArray|array $expression): LastOperator
    {
        return new LastOperator($expression);
    }

    /**
     * Returns a specified number of elements from the end of an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN-array-element/
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return n elements.
     */
    public static function lastN(
        ResolvesToInt|int $n,
        PackedArray|ResolvesToArray|BSONArray|array $input,
    ): LastNOperator
    {
        return new LastNOperator($n, $input);
    }

    /**
     * Defines variables for use within the scope of a subexpression and returns the result of the subexpression. Accepts named parameters.
     * Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/let/
     * @param Document|Serializable|array|stdClass $vars Assignment block for the variables accessible in the in expression. To assign a variable, specify a string for the variable name and assign a valid expression for the value.
     * The variable assignments have no meaning outside the in expression, not even within the vars block itself.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in The expression to evaluate.
     */
    public static function let(
        Document|Serializable|stdClass|array $vars,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in,
    ): LetOperator
    {
        return new LetOperator($vars, $in);
    }

    /**
     * Return a value without parsing. Use for values that the aggregation pipeline may interpret as an expression. For example, use a $literal expression to a string that starts with a dollar sign ($) to avoid parsing as a field path.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/literal/
     * @param Type|array|bool|float|int|null|stdClass|string $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression.
     */
    public static function literal(Type|stdClass|array|bool|float|int|null|string $value): LiteralOperator
    {
        return new LiteralOperator($value);
    }

    /**
     * Calculates the natural log of a number.
     * $ln is equivalent to $log: [ <number>, Math.E ] expression, where Math.E is a JavaScript representation for Euler's number e.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ln/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number. For more information on expressions, see Expressions.
     */
    public static function ln(Decimal128|Int64|ResolvesToNumber|float|int $number): LnOperator
    {
        return new LnOperator($number);
    }

    /**
     * Calculates the log of a number in the specified base.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number.
     * @param Decimal128|Int64|ResolvesToNumber|float|int $base Any valid expression as long as it resolves to a positive number greater than 1.
     */
    public static function log(
        Decimal128|Int64|ResolvesToNumber|float|int $number,
        Decimal128|Int64|ResolvesToNumber|float|int $base,
    ): LogOperator
    {
        return new LogOperator($number, $base);
    }

    /**
     * Calculates the log base 10 of a number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log10/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number.
     */
    public static function log10(Decimal128|Int64|ResolvesToNumber|float|int $number): Log10Operator
    {
        return new Log10Operator($number);
    }

    /**
     * Returns true if the first value is less than the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lt/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2
     */
    public static function lt(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ): LtOperator
    {
        return new LtOperator($expression1, $expression2);
    }

    /**
     * Returns true if the first value is less than or equal to the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lte/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2
     */
    public static function lte(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ): LteOperator
    {
        return new LteOperator($expression1, $expression2);
    }

    /**
     * Removes whitespace or the specified characters from the beginning of a string.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ltrim/
     * @param ResolvesToString|string $input The string to trim. The argument can be any valid expression that resolves to a string.
     * @param Optional|ResolvesToString|string $chars The character(s) to trim from the beginning of the input.
     * The argument can be any valid expression that resolves to a string. The $ltrim operator breaks down the string into individual UTF code point to trim from input.
     * If unspecified, $ltrim removes whitespace characters, including the null character.
     */
    public static function ltrim(
        ResolvesToString|string $input,
        Optional|ResolvesToString|string $chars = Optional::Undefined,
    ): LtrimOperator
    {
        return new LtrimOperator($input, $chars);
    }

    /**
     * Applies a subexpression to each element of an array and returns the array of resulting values in order. Accepts named parameters.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to an array.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as.
     * @param Optional|ResolvesToString|string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     */
    public static function map(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in,
        Optional|ResolvesToString|string $as = Optional::Undefined,
    ): MapOperator
    {
        return new MapOperator($input, $in, $as);
    }

    /**
     * Returns the maximum value that results from applying an expression to each document.
     * Changed in MongoDB 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/
     * @no-named-arguments
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$expression
     */
    public static function max(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ): MaxOperator
    {
        return new MaxOperator(...$expression);
    }

    /**
     * Returns the n largest values in an array. Distinct from the $maxN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN-array-element/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return the maximal n elements.
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns.
     */
    public static function maxN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToInt|int $n,
    ): MaxNOperator
    {
        return new MaxNOperator($input, $n);
    }

    /**
     * Returns an approximation of the median, the 50th percentile, as a scalar value.
     * New in MongoDB 7.0.
     * This operator is available as an accumulator in these stages:
     * $group
     * $setWindowFields
     * It is also available as an aggregation expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/
     * @param BSONArray|Decimal128|Int64|PackedArray|ResolvesToNumber|array|float|int $input $median calculates the 50th percentile value of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $median calculation ignores it.
     * @param string $method The method that mongod uses to calculate the 50th percentile value. The method must be 'approximate'.
     */
    public static function median(
        Decimal128|Int64|PackedArray|ResolvesToNumber|BSONArray|array|float|int $input,
        string $method,
    ): MedianOperator
    {
        return new MedianOperator($input, $method);
    }

    /**
     * Combines multiple documents into a single document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/
     * @no-named-arguments
     * @param Document|ResolvesToObject|Serializable|array|stdClass ...$document Any valid expression that resolves to a document.
     */
    public static function mergeObjects(
        Document|Serializable|ResolvesToObject|stdClass|array ...$document,
    ): MergeObjectsOperator
    {
        return new MergeObjectsOperator(...$document);
    }

    /**
     * Access available per-document metadata related to the aggregation operation.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/
     * @param string $keyword
     */
    public static function meta(string $keyword): MetaOperator
    {
        return new MetaOperator($keyword);
    }

    /**
     * Returns the milliseconds of a date as a number between 0 and 999.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/millisecond/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function millisecond(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): MillisecondOperator
    {
        return new MillisecondOperator($date, $timezone);
    }

    /**
     * Returns the minimum value that results from applying an expression to each document.
     * Changed in MongoDB 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/
     * @no-named-arguments
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$expression
     */
    public static function min(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ): MinOperator
    {
        return new MinOperator(...$expression);
    }

    /**
     * Returns the n smallest values in an array. Distinct from the $minN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN-array-element/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return the maximal n elements.
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns.
     */
    public static function minN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToInt|int $n,
    ): MinNOperator
    {
        return new MinNOperator($input, $n);
    }

    /**
     * Returns the minute for a date as a number between 0 and 59.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minute/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function minute(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): MinuteOperator
    {
        return new MinuteOperator($date, $timezone);
    }

    /**
     * Returns the remainder of the first number divided by the second. Accepts two argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mod/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $dividend The first argument is the dividend, and the second argument is the divisor; i.e. first argument is divided by the second argument.
     * @param Decimal128|Int64|ResolvesToNumber|float|int $divisor
     */
    public static function mod(
        Decimal128|Int64|ResolvesToNumber|float|int $dividend,
        Decimal128|Int64|ResolvesToNumber|float|int $divisor,
    ): ModOperator
    {
        return new ModOperator($dividend, $divisor);
    }

    /**
     * Returns the month for a date as a number between 1 (January) and 12 (December).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/month/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function month(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): MonthOperator
    {
        return new MonthOperator($date, $timezone);
    }

    /**
     * Multiplies numbers to return the product. Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/multiply/
     * @no-named-arguments
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression The arguments can be any valid expression as long as they resolve to numbers.
     * Starting in MongoDB 6.1 you can optimize the $multiply operation. To improve performance, group references at the end of the argument list.
     */
    public static function multiply(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): MultiplyOperator
    {
        return new MultiplyOperator(...$expression);
    }

    /**
     * Returns true if the values are not equivalent.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ne/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2
     */
    public static function ne(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ): NeOperator
    {
        return new NeOperator($expression1, $expression2);
    }

    /**
     * Returns the boolean value that is the opposite of its argument expression. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/not/
     * @param ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function not(
        Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): NotOperator
    {
        return new NotOperator($expression);
    }

    /**
     * Converts a document to an array of documents representing key-value pairs.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/
     * @param Document|ResolvesToObject|Serializable|array|stdClass $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields.
     */
    public static function objectToArray(
        Document|Serializable|ResolvesToObject|stdClass|array $object,
    ): ObjectToArrayOperator
    {
        return new ObjectToArrayOperator($object);
    }

    /**
     * Returns true when any of its expressions evaluates to true. Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/or/
     * @no-named-arguments
     * @param ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|null|stdClass|string ...$expression
     */
    public static function or(
        Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ): OrOperator
    {
        return new OrOperator(...$expression);
    }

    /**
     * Returns an array of scalar values that correspond to specified percentile values.
     * New in MongoDB 7.0.
     *
     * This operator is available as an accumulator in these stages:
     * $group
     *
     * $setWindowFields
     *
     * It is also available as an aggregation expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/
     * @param BSONArray|Decimal128|Int64|PackedArray|ResolvesToNumber|array|float|int $input $percentile calculates the percentile values of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $percentile calculation ignores it.
     * @param BSONArray|PackedArray|ResolvesToArray|array $p $percentile calculates a percentile value for each element in p. The elements represent percentages and must evaluate to numeric values in the range 0.0 to 1.0, inclusive.
     * $percentile returns results in the same order as the elements in p.
     * @param string $method The method that mongod uses to calculate the percentile value. The method must be 'approximate'.
     */
    public static function percentile(
        Decimal128|Int64|PackedArray|ResolvesToNumber|BSONArray|array|float|int $input,
        PackedArray|ResolvesToArray|BSONArray|array $p,
        string $method,
    ): PercentileOperator
    {
        return new PercentileOperator($input, $p, $method);
    }

    /**
     * Raises a number to the specified exponent.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/pow/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number
     * @param Decimal128|Int64|ResolvesToNumber|float|int $exponent
     */
    public static function pow(
        Decimal128|Int64|ResolvesToNumber|float|int $number,
        Decimal128|Int64|ResolvesToNumber|float|int $exponent,
    ): PowOperator
    {
        return new PowOperator($number, $exponent);
    }

    /**
     * Converts a value from radians to degrees.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/radiansToDegrees/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public static function radiansToDegrees(
        Decimal128|Int64|ResolvesToNumber|float|int $expression,
    ): RadiansToDegreesOperator
    {
        return new RadiansToDegreesOperator($expression);
    }

    /**
     * Returns a random float between 0 and 1
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rand/
     */
    public static function rand(): RandOperator
    {
        return new RandOperator();
    }

    /**
     * Outputs an array containing a sequence of integers according to user-defined inputs.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/range/
     * @param ResolvesToInt|int $start An integer that specifies the start of the sequence. Can be any valid expression that resolves to an integer.
     * @param ResolvesToInt|int $end An integer that specifies the exclusive upper limit of the sequence. Can be any valid expression that resolves to an integer.
     * @param Optional|ResolvesToInt|int $step An integer that specifies the increment value. Can be any valid expression that resolves to a non-zero integer. Defaults to 1.
     */
    public static function range(
        ResolvesToInt|int $start,
        ResolvesToInt|int $end,
        Optional|ResolvesToInt|int $step = Optional::Undefined,
    ): RangeOperator
    {
        return new RangeOperator($start, $end, $step);
    }

    /**
     * Applies an expression to each element in an array and combines them into a single value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input Can be any valid expression that resolves to an array.
     * If the argument resolves to a value of null or refers to a missing field, $reduce returns null.
     * If the argument does not resolve to an array or null nor refers to a missing field, $reduce returns an error.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $initialValue The initial cumulative value set before in is applied to the first element of the input array.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $in A valid expression that $reduce applies to each element in the input array in left-to-right order. Wrap the input value with $reverseArray to yield the equivalent of applying the combining expression from right-to-left.
     * During evaluation of the in expression, two variables will be available:
     * - value is the variable that represents the cumulative value of the expression.
     * - this is the variable that refers to the element being processed.
     */
    public static function reduce(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $initialValue,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $in,
    ): ReduceOperator
    {
        return new ReduceOperator($input, $initialValue, $in);
    }

    /**
     * Applies a regular expression (regex) to a string and returns information on the first matched substring.
     * New in MongoDB 4.2.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFind/
     * @param ResolvesToString|string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string.
     * @param Regex|ResolvesToString|string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options)
     * @param Optional|string $options
     */
    public static function regexFind(
        ResolvesToString|string $input,
        Regex|ResolvesToString|string $regex,
        Optional|string $options = Optional::Undefined,
    ): RegexFindOperator
    {
        return new RegexFindOperator($input, $regex, $options);
    }

    /**
     * Applies a regular expression (regex) to a string and returns information on the all matched substrings.
     * New in MongoDB 4.2.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/
     * @param ResolvesToString|string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string.
     * @param Regex|ResolvesToString|string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options)
     * @param Optional|string $options
     */
    public static function regexFindAll(
        ResolvesToString|string $input,
        Regex|ResolvesToString|string $regex,
        Optional|string $options = Optional::Undefined,
    ): RegexFindAllOperator
    {
        return new RegexFindAllOperator($input, $regex, $options);
    }

    /**
     * Applies a regular expression (regex) to a string and returns a boolean that indicates if a match is found or not.
     * New in MongoDB 4.2.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/
     * @param ResolvesToString|string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string.
     * @param Regex|ResolvesToString|string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options)
     * @param Optional|string $options
     */
    public static function regexMatch(
        ResolvesToString|string $input,
        Regex|ResolvesToString|string $regex,
        Optional|string $options = Optional::Undefined,
    ): RegexMatchOperator
    {
        return new RegexMatchOperator($input, $regex, $options);
    }

    /**
     * Replaces all instances of a search string in an input string with a replacement string.
     * $replaceAll is both case-sensitive and diacritic-sensitive, and ignores any collation present on a collection.
     * New in MongoDB 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceAll/
     * @param ResolvesToNull|ResolvesToString|null|string $input The string on which you wish to apply the find. Can be any valid expression that resolves to a string or a null. If input refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|null|string $find The string to search for within the given input. Can be any valid expression that resolves to a string or a null. If find refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|null|string $replacement The string to use to replace all matched instances of find in input. Can be any valid expression that resolves to a string or a null.
     */
    public static function replaceAll(
        ResolvesToNull|ResolvesToString|null|string $input,
        ResolvesToNull|ResolvesToString|null|string $find,
        ResolvesToNull|ResolvesToString|null|string $replacement,
    ): ReplaceAllOperator
    {
        return new ReplaceAllOperator($input, $find, $replacement);
    }

    /**
     * Replaces the first instance of a matched string in a given input.
     * New in MongoDB 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceOne/
     * @param ResolvesToNull|ResolvesToString|null|string $input The string on which you wish to apply the find. Can be any valid expression that resolves to a string or a null. If input refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|null|string $find The string to search for within the given input. Can be any valid expression that resolves to a string or a null. If find refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|null|string $replacement The string to use to replace all matched instances of find in input. Can be any valid expression that resolves to a string or a null.
     */
    public static function replaceOne(
        ResolvesToNull|ResolvesToString|null|string $input,
        ResolvesToNull|ResolvesToString|null|string $find,
        ResolvesToNull|ResolvesToString|null|string $replacement,
    ): ReplaceOneOperator
    {
        return new ReplaceOneOperator($input, $find, $replacement);
    }

    /**
     * Returns an array with the elements in reverse order.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reverseArray/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression The argument can be any valid expression as long as it resolves to an array.
     */
    public static function reverseArray(PackedArray|ResolvesToArray|BSONArray|array $expression): ReverseArrayOperator
    {
        return new ReverseArrayOperator($expression);
    }

    /**
     * Rounds a number to to a whole integer or to a specified decimal place.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/round/
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $round returns an error if the expression resolves to a non-numeric data type.
     * @param Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive.
     */
    public static function round(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $number,
        Optional|ResolvesToInt|int $place = Optional::Undefined,
    ): RoundOperator
    {
        return new RoundOperator($number, $place);
    }

    /**
     * Removes whitespace characters, including null, or the specified characters from the end of a string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rtrim/
     * @param ResolvesToString|string $input The string to trim. The argument can be any valid expression that resolves to a string.
     * @param Optional|ResolvesToString|string $chars The character(s) to trim from the beginning of the input.
     * The argument can be any valid expression that resolves to a string. The $ltrim operator breaks down the string into individual UTF code point to trim from input.
     * If unspecified, $ltrim removes whitespace characters, including the null character.
     */
    public static function rtrim(
        ResolvesToString|string $input,
        Optional|ResolvesToString|string $chars = Optional::Undefined,
    ): RtrimOperator
    {
        return new RtrimOperator($input, $chars);
    }

    /**
     * Returns the seconds for a date as a number between 0 and 60 (leap seconds).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/second/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function second(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): SecondOperator
    {
        return new SecondOperator($date, $timezone);
    }

    /**
     * Returns a set with elements that appear in the first set but not in the second set; i.e. performs a relative complement of the second set relative to the first. Accepts exactly two argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setDifference/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression1 The arguments can be any valid expression as long as they each resolve to an array.
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression2 The arguments can be any valid expression as long as they each resolve to an array.
     */
    public static function setDifference(
        PackedArray|ResolvesToArray|BSONArray|array $expression1,
        PackedArray|ResolvesToArray|BSONArray|array $expression2,
    ): SetDifferenceOperator
    {
        return new SetDifferenceOperator($expression1, $expression2);
    }

    /**
     * Returns true if the input sets have the same distinct elements. Accepts two or more argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setEquals/
     * @no-named-arguments
     * @param BSONArray|PackedArray|ResolvesToArray|array ...$expression
     */
    public static function setEquals(PackedArray|ResolvesToArray|BSONArray|array ...$expression): SetEqualsOperator
    {
        return new SetEqualsOperator(...$expression);
    }

    /**
     * Adds, updates, or removes a specified field in a document. You can use $setField to add, update, or remove fields with names that contain periods (.) or start with dollar signs ($).
     * New in MongoDB 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/
     * @param ResolvesToString|string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant.
     * @param Document|ResolvesToObject|Serializable|array|stdClass $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined.
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $value The value that you want to assign to field. value can be any valid expression.
     * Set to $$REMOVE to remove field from the input document.
     */
    public static function setField(
        ResolvesToString|string $field,
        Document|Serializable|ResolvesToObject|stdClass|array $input,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $value,
    ): SetFieldOperator
    {
        return new SetFieldOperator($field, $input, $value);
    }

    /**
     * Returns a set with elements that appear in all of the input sets. Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIntersection/
     * @no-named-arguments
     * @param BSONArray|PackedArray|ResolvesToArray|array ...$expression
     */
    public static function setIntersection(
        PackedArray|ResolvesToArray|BSONArray|array ...$expression,
    ): SetIntersectionOperator
    {
        return new SetIntersectionOperator(...$expression);
    }

    /**
     * Returns true if all elements of the first set appear in the second set, including when the first set equals the second set; i.e. not a strict subset. Accepts exactly two argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIsSubset/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression1
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression2
     */
    public static function setIsSubset(
        PackedArray|ResolvesToArray|BSONArray|array $expression1,
        PackedArray|ResolvesToArray|BSONArray|array $expression2,
    ): SetIsSubsetOperator
    {
        return new SetIsSubsetOperator($expression1, $expression2);
    }

    /**
     * Returns a set with elements that appear in any of the input sets.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setUnion/
     * @no-named-arguments
     * @param BSONArray|PackedArray|ResolvesToArray|array ...$expression
     */
    public static function setUnion(PackedArray|ResolvesToArray|BSONArray|array ...$expression): SetUnionOperator
    {
        return new SetUnionOperator(...$expression);
    }

    /**
     * Returns the sine of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sin/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $sin takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $sin returns values as a double. $sin can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function sin(Decimal128|Int64|ResolvesToNumber|float|int $expression): SinOperator
    {
        return new SinOperator($expression);
    }

    /**
     * Returns the hyperbolic sine of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sinh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $sinh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $sinh returns values as a double. $sinh can also return values as a 128-bit decimal if the expression resolves to a 128-bit decimal value.
     */
    public static function sinh(Decimal128|Int64|ResolvesToNumber|float|int $expression): SinhOperator
    {
        return new SinhOperator($expression);
    }

    /**
     * Returns the number of elements in the array. Accepts a single expression as argument.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/size/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression The argument for $size can be any expression as long as it resolves to an array.
     */
    public static function size(PackedArray|ResolvesToArray|BSONArray|array $expression): SizeOperator
    {
        return new SizeOperator($expression);
    }

    /**
     * Returns a subset of an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/slice/
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression Any valid expression as long as it resolves to an array.
     * @param ResolvesToInt|int $n Any valid expression as long as it resolves to an integer. If position is specified, n must resolve to a positive integer.
     * If positive, $slice returns up to the first n elements in the array. If the position is specified, $slice returns the first n elements starting from the position.
     * If negative, $slice returns up to the last n elements in the array. n cannot resolve to a negative number if <position> is specified.
     * @param Optional|ResolvesToInt|int $position Any valid expression as long as it resolves to an integer.
     * If positive, $slice determines the starting position from the start of the array. If position is greater than the number of elements, the $slice returns an empty array.
     * If negative, $slice determines the starting position from the end of the array. If the absolute value of the <position> is greater than the number of elements, the starting position is the start of the array.
     */
    public static function slice(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
        ResolvesToInt|int $n,
        Optional|ResolvesToInt|int $position = Optional::Undefined,
    ): SliceOperator
    {
        return new SliceOperator($expression, $n, $position);
    }

    /**
     * Sorts the elements of an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input The array to be sorted.
     * The result is null if the expression: is missing, evaluates to null, or evaluates to undefined
     * If the expression evaluates to any other non-array value, the document returns an error.
     * @param Document|Serializable|array|int|stdClass $sortBy The document specifies a sort ordering.
     */
    public static function sortArray(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Document|Serializable|stdClass|array|int $sortBy,
    ): SortArrayOperator
    {
        return new SortArrayOperator($input, $sortBy);
    }

    /**
     * Splits a string into substrings based on a delimiter. Returns an array of substrings. If the delimiter is not found within the string, returns an array containing the original string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/split/
     * @param ResolvesToString|string $string The string to be split. string expression can be any valid expression as long as it resolves to a string.
     * @param ResolvesToString|string $delimiter The delimiter to use when splitting the string expression. delimiter can be any valid expression as long as it resolves to a string.
     */
    public static function split(ResolvesToString|string $string, ResolvesToString|string $delimiter): SplitOperator
    {
        return new SplitOperator($string, $delimiter);
    }

    /**
     * Calculates the square root.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sqrt/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number The argument can be any valid expression as long as it resolves to a non-negative number.
     */
    public static function sqrt(Decimal128|Int64|ResolvesToNumber|float|int $number): SqrtOperator
    {
        return new SqrtOperator($number);
    }

    /**
     * Calculates the population standard deviation of the input values. Use if the values encompass the entire population of data you want to represent and do not wish to generalize about a larger population. $stdDevPop ignores non-numeric values.
     * If the values represent only a sample of a population of data from which to generalize about the population, use $stdDevSamp instead.
     * Changed in MongoDB 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/
     * @no-named-arguments
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function stdDevPop(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): StdDevPopOperator
    {
        return new StdDevPopOperator(...$expression);
    }

    /**
     * Calculates the sample standard deviation of the input values. Use if the values encompass a sample of a population of data from which to generalize about the population. $stdDevSamp ignores non-numeric values.
     * If the values represent the entire population of data or you do not wish to generalize about a larger population, use $stdDevPop instead.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/
     * @no-named-arguments
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function stdDevSamp(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): StdDevSampOperator
    {
        return new StdDevSampOperator(...$expression);
    }

    /**
     * Performs case-insensitive string comparison and returns: 0 if two strings are equivalent, 1 if the first string is greater than the second, and -1 if the first string is less than the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strcasecmp/
     * @param ResolvesToString|string $expression1
     * @param ResolvesToString|string $expression2
     */
    public static function strcasecmp(
        ResolvesToString|string $expression1,
        ResolvesToString|string $expression2,
    ): StrcasecmpOperator
    {
        return new StrcasecmpOperator($expression1, $expression2);
    }

    /**
     * Returns the number of UTF-8 encoded bytes in a string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenBytes/
     * @param ResolvesToString|string $expression
     */
    public static function strLenBytes(ResolvesToString|string $expression): StrLenBytesOperator
    {
        return new StrLenBytesOperator($expression);
    }

    /**
     * Returns the number of UTF-8 code points in a string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenCP/
     * @param ResolvesToString|string $expression
     */
    public static function strLenCP(ResolvesToString|string $expression): StrLenCPOperator
    {
        return new StrLenCPOperator($expression);
    }

    /**
     * Deprecated. Use $substrBytes or $substrCP.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substr/
     * @param ResolvesToString|string $string
     * @param ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "".
     * @param ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public static function substr(
        ResolvesToString|string $string,
        ResolvesToInt|int $start,
        ResolvesToInt|int $length,
    ): SubstrOperator
    {
        return new SubstrOperator($string, $start, $length);
    }

    /**
     * Returns the substring of a string. Starts with the character at the specified UTF-8 byte index (zero-based) in the string and continues for the specified number of bytes.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrBytes/
     * @param ResolvesToString|string $string
     * @param ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "".
     * @param ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public static function substrBytes(
        ResolvesToString|string $string,
        ResolvesToInt|int $start,
        ResolvesToInt|int $length,
    ): SubstrBytesOperator
    {
        return new SubstrBytesOperator($string, $start, $length);
    }

    /**
     * Returns the substring of a string. Starts with the character at the specified UTF-8 code point (CP) index (zero-based) in the string and continues for the number of code points specified.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrCP/
     * @param ResolvesToString|string $string
     * @param ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "".
     * @param ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public static function substrCP(
        ResolvesToString|string $string,
        ResolvesToInt|int $start,
        ResolvesToInt|int $length,
    ): SubstrCPOperator
    {
        return new SubstrCPOperator($string, $start, $length);
    }

    /**
     * Returns the result of subtracting the second value from the first. If the two values are numbers, return the difference. If the two values are dates, return the difference in milliseconds. If the two values are a date and a number in milliseconds, return the resulting date. Accepts two argument expressions. If the two values are a date and a number, specify the date argument first as it is not meaningful to subtract a date from a number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression1
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression2
     */
    public static function subtract(
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $expression1,
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $expression2,
    ): SubtractOperator
    {
        return new SubtractOperator($expression1, $expression2);
    }

    /**
     * Returns a sum of numerical values. Ignores non-numeric values.
     * Changed in MongoDB 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/
     * @no-named-arguments
     * @param BSONArray|Decimal128|Int64|PackedArray|ResolvesToArray|ResolvesToNumber|array|float|int ...$expression
     */
    public static function sum(
        Decimal128|Int64|PackedArray|ResolvesToArray|ResolvesToNumber|BSONArray|array|float|int ...$expression,
    ): SumOperator
    {
        return new SumOperator(...$expression);
    }

    /**
     * Evaluates a series of case expressions. When it finds an expression which evaluates to true, $switch executes a specified expression and breaks out of the control flow.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/
     * @param BSONArray|PackedArray|array $branches An array of control branch documents. Each branch is a document with the following fields:
     * - case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * - then Can be any valid expression.
     * The branches array must contain at least one branch document.
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $default The path to take if no branch case expression evaluates to true.
     * Although optional, if default is unspecified and no branch case evaluates to true, $switch returns an error.
     */
    public static function switch(
        PackedArray|BSONArray|array $branches,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $default = Optional::Undefined,
    ): SwitchOperator
    {
        return new SwitchOperator($branches, $default);
    }

    /**
     * Returns the tangent of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tan/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $tan takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $tan returns values as a double. $tan can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function tan(Decimal128|Int64|ResolvesToNumber|float|int $expression): TanOperator
    {
        return new TanOperator($expression);
    }

    /**
     * Returns the hyperbolic tangent of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tanh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $tanh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $tanh returns values as a double. $tanh can also return values as a 128-bit decimal if the expression resolves to a 128-bit decimal value.
     */
    public static function tanh(Decimal128|Int64|ResolvesToNumber|float|int $expression): TanhOperator
    {
        return new TanhOperator($expression);
    }

    /**
     * Converts value to a boolean.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toBool/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function toBool(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): ToBoolOperator
    {
        return new ToBoolOperator($expression);
    }

    /**
     * Converts value to a Date.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDate/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function toDate(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): ToDateOperator
    {
        return new ToDateOperator($expression);
    }

    /**
     * Converts value to a Decimal128.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDecimal/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function toDecimal(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): ToDecimalOperator
    {
        return new ToDecimalOperator($expression);
    }

    /**
     * Converts value to a double.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDouble/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function toDouble(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): ToDoubleOperator
    {
        return new ToDoubleOperator($expression);
    }

    /**
     * Computes and returns the hash value of the input expression using the same hash function that MongoDB uses to create a hashed index. A hash function maps a key or string to a fixed-size numeric value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toHashedIndexKey/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $value key or string to hash
     */
    public static function toHashedIndexKey(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $value,
    ): ToHashedIndexKeyOperator
    {
        return new ToHashedIndexKeyOperator($value);
    }

    /**
     * Converts value to an integer.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toInt/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function toInt(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): ToIntOperator
    {
        return new ToIntOperator($expression);
    }

    /**
     * Converts value to a long.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLong/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function toLong(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): ToLongOperator
    {
        return new ToLongOperator($expression);
    }

    /**
     * Converts a string to lowercase. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLower/
     * @param ResolvesToString|string $expression
     */
    public static function toLower(ResolvesToString|string $expression): ToLowerOperator
    {
        return new ToLowerOperator($expression);
    }

    /**
     * Converts value to an ObjectId.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toObjectId/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function toObjectId(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): ToObjectIdOperator
    {
        return new ToObjectIdOperator($expression);
    }

    /**
     * Converts value to a string.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toString/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function toString(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): ToStringOperator
    {
        return new ToStringOperator($expression);
    }

    /**
     * Converts a string to uppercase. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toUpper/
     * @param ResolvesToString|string $expression
     */
    public static function toUpper(ResolvesToString|string $expression): ToUpperOperator
    {
        return new ToUpperOperator($expression);
    }

    /**
     * Removes whitespace or the specified characters from the beginning and end of a string.
     * New in MongoDB 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trim/
     * @param ResolvesToString|string $input The string to trim. The argument can be any valid expression that resolves to a string.
     * @param Optional|ResolvesToString|string $chars The character(s) to trim from the beginning of the input.
     * The argument can be any valid expression that resolves to a string. The $ltrim operator breaks down the string into individual UTF code point to trim from input.
     * If unspecified, $ltrim removes whitespace characters, including the null character.
     */
    public static function trim(
        ResolvesToString|string $input,
        Optional|ResolvesToString|string $chars = Optional::Undefined,
    ): TrimOperator
    {
        return new TrimOperator($input, $chars);
    }

    /**
     * Truncates a number to a whole integer or to a specified decimal place.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trunc/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $trunc returns an error if the expression resolves to a non-numeric data type.
     * @param Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. e.g. -20 < place < 100. Defaults to 0.
     */
    public static function trunc(
        Decimal128|Int64|ResolvesToNumber|float|int $number,
        Optional|ResolvesToInt|int $place = Optional::Undefined,
    ): TruncOperator
    {
        return new TruncOperator($number, $place);
    }

    /**
     * Returns the incrementing ordinal from a timestamp as a long.
     * New in MongoDB 5.1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsIncrement/
     * @param ResolvesToTimestamp|Timestamp|int $expression
     */
    public static function tsIncrement(Timestamp|ResolvesToTimestamp|int $expression): TsIncrementOperator
    {
        return new TsIncrementOperator($expression);
    }

    /**
     * Returns the seconds from a timestamp as a long.
     * New in MongoDB 5.1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/
     * @param ResolvesToTimestamp|Timestamp|int $expression
     */
    public static function tsSecond(Timestamp|ResolvesToTimestamp|int $expression): TsSecondOperator
    {
        return new TsSecondOperator($expression);
    }

    /**
     * Return the BSON data type of the field.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/type/
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public static function type(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ): TypeOperator
    {
        return new TypeOperator($expression);
    }

    /**
     * You can use $unsetField to remove fields with names that contain periods (.) or that start with dollar signs ($).
     * $unsetField is an alias for $setField using $$REMOVE to remove fields.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/
     * @param ResolvesToString|string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant.
     * @param Document|ResolvesToObject|Serializable|array|stdClass $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined.
     */
    public static function unsetField(
        ResolvesToString|string $field,
        Document|Serializable|ResolvesToObject|stdClass|array $input,
    ): UnsetFieldOperator
    {
        return new UnsetFieldOperator($field, $input);
    }

    /**
     * Returns the week number for a date as a number between 0 (the partial week that precedes the first Sunday of the year) and 53 (leap year).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/week/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function week(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): WeekOperator
    {
        return new WeekOperator($date, $timezone);
    }

    /**
     * Returns the year for a date as a number (e.g. 2014).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/year/
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function year(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ): YearOperator
    {
        return new YearOperator($date, $timezone);
    }

    /**
     * Merge two arrays together.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/zip/
     * @param BSONArray|PackedArray|ResolvesToArray|array $inputs An array of expressions that resolve to arrays. The elements of these input arrays combine to form the arrays of the output array.
     * If any of the inputs arrays resolves to a value of null or refers to a missing field, $zip returns null.
     * If any of the inputs arrays does not resolve to an array or null nor refers to a missing field, $zip returns an error.
     * @param Optional|bool $useLongestLength A boolean which specifies whether the length of the longest array determines the number of arrays in the output array.
     * The default value is false: the shortest array length determines the number of arrays in the output array.
     * @param Optional|BSONArray|PackedArray|array $defaults An array of default element values to use if the input arrays have different lengths. You must specify useLongestLength: true along with this field, or else $zip will return an error.
     * If useLongestLength: true but defaults is empty or not specified, $zip uses null as the default value.
     * If specifying a non-empty defaults, you must specify a default for each input array or else $zip will return an error.
     */
    public static function zip(
        PackedArray|ResolvesToArray|BSONArray|array $inputs,
        Optional|bool $useLongestLength = Optional::Undefined,
        Optional|PackedArray|BSONArray|array $defaults = Optional::Undefined,
    ): ZipOperator
    {
        return new ZipOperator($inputs, $useLongestLength, $defaults);
    }
}
