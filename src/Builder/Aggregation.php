<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use DateTimeInterface;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Aggregation\AbsAggregation;
use MongoDB\Builder\Aggregation\AccumulatorAggregation;
use MongoDB\Builder\Aggregation\AcosAggregation;
use MongoDB\Builder\Aggregation\AcoshAggregation;
use MongoDB\Builder\Aggregation\AddAggregation;
use MongoDB\Builder\Aggregation\AddToSetAggregation;
use MongoDB\Builder\Aggregation\AllElementsTrueAggregation;
use MongoDB\Builder\Aggregation\AndAggregation;
use MongoDB\Builder\Aggregation\AnyElementTrueAggregation;
use MongoDB\Builder\Aggregation\ArrayElemAtAggregation;
use MongoDB\Builder\Aggregation\ArrayToObjectAggregation;
use MongoDB\Builder\Aggregation\AsinAggregation;
use MongoDB\Builder\Aggregation\AsinhAggregation;
use MongoDB\Builder\Aggregation\Atan2Aggregation;
use MongoDB\Builder\Aggregation\AtanAggregation;
use MongoDB\Builder\Aggregation\AtanhAggregation;
use MongoDB\Builder\Aggregation\AvgAggregation;
use MongoDB\Builder\Aggregation\BinarySizeAggregation;
use MongoDB\Builder\Aggregation\BitAndAggregation;
use MongoDB\Builder\Aggregation\BitNotAggregation;
use MongoDB\Builder\Aggregation\BitOrAggregation;
use MongoDB\Builder\Aggregation\BitXorAggregation;
use MongoDB\Builder\Aggregation\BottomAggregation;
use MongoDB\Builder\Aggregation\BottomNAggregation;
use MongoDB\Builder\Aggregation\BsonSizeAggregation;
use MongoDB\Builder\Aggregation\CeilAggregation;
use MongoDB\Builder\Aggregation\CmpAggregation;
use MongoDB\Builder\Aggregation\ConcatAggregation;
use MongoDB\Builder\Aggregation\ConcatArraysAggregation;
use MongoDB\Builder\Aggregation\CondAggregation;
use MongoDB\Builder\Aggregation\ConvertAggregation;
use MongoDB\Builder\Aggregation\CosAggregation;
use MongoDB\Builder\Aggregation\CoshAggregation;
use MongoDB\Builder\Aggregation\CountAggregation;
use MongoDB\Builder\Aggregation\CovariancePopAggregation;
use MongoDB\Builder\Aggregation\CovarianceSampAggregation;
use MongoDB\Builder\Aggregation\DateAddAggregation;
use MongoDB\Builder\Aggregation\DateDiffAggregation;
use MongoDB\Builder\Aggregation\DateFromPartsAggregation;
use MongoDB\Builder\Aggregation\DateFromStringAggregation;
use MongoDB\Builder\Aggregation\DateSubtractAggregation;
use MongoDB\Builder\Aggregation\DateToPartsAggregation;
use MongoDB\Builder\Aggregation\DateToStringAggregation;
use MongoDB\Builder\Aggregation\DateTruncAggregation;
use MongoDB\Builder\Aggregation\DayOfMonthAggregation;
use MongoDB\Builder\Aggregation\DayOfWeekAggregation;
use MongoDB\Builder\Aggregation\DayOfYearAggregation;
use MongoDB\Builder\Aggregation\DegreesToRadiansAggregation;
use MongoDB\Builder\Aggregation\DenseRankAggregation;
use MongoDB\Builder\Aggregation\DerivativeAggregation;
use MongoDB\Builder\Aggregation\DivideAggregation;
use MongoDB\Builder\Aggregation\DocumentNumberAggregation;
use MongoDB\Builder\Aggregation\EqAggregation;
use MongoDB\Builder\Aggregation\ExpAggregation;
use MongoDB\Builder\Aggregation\ExpMovingAvgAggregation;
use MongoDB\Builder\Aggregation\FilterAggregation;
use MongoDB\Builder\Aggregation\FirstAggregation;
use MongoDB\Builder\Aggregation\FirstNAggregation;
use MongoDB\Builder\Aggregation\FloorAggregation;
use MongoDB\Builder\Aggregation\FunctionAggregation;
use MongoDB\Builder\Aggregation\GetFieldAggregation;
use MongoDB\Builder\Aggregation\GtAggregation;
use MongoDB\Builder\Aggregation\GteAggregation;
use MongoDB\Builder\Aggregation\HourAggregation;
use MongoDB\Builder\Aggregation\IfNullAggregation;
use MongoDB\Builder\Aggregation\InAggregation;
use MongoDB\Builder\Aggregation\IndexOfArrayAggregation;
use MongoDB\Builder\Aggregation\IndexOfBytesAggregation;
use MongoDB\Builder\Aggregation\IndexOfCPAggregation;
use MongoDB\Builder\Aggregation\IntegralAggregation;
use MongoDB\Builder\Aggregation\IsArrayAggregation;
use MongoDB\Builder\Aggregation\IsNumberAggregation;
use MongoDB\Builder\Aggregation\IsoDayOfWeekAggregation;
use MongoDB\Builder\Aggregation\IsoWeekAggregation;
use MongoDB\Builder\Aggregation\IsoWeekYearAggregation;
use MongoDB\Builder\Aggregation\LastAggregation;
use MongoDB\Builder\Aggregation\LastNAggregation;
use MongoDB\Builder\Aggregation\LetAggregation;
use MongoDB\Builder\Aggregation\LinearFillAggregation;
use MongoDB\Builder\Aggregation\LiteralAggregation;
use MongoDB\Builder\Aggregation\LnAggregation;
use MongoDB\Builder\Aggregation\LocfAggregation;
use MongoDB\Builder\Aggregation\Log10Aggregation;
use MongoDB\Builder\Aggregation\LogAggregation;
use MongoDB\Builder\Aggregation\LtAggregation;
use MongoDB\Builder\Aggregation\LteAggregation;
use MongoDB\Builder\Aggregation\LtrimAggregation;
use MongoDB\Builder\Aggregation\MapAggregation;
use MongoDB\Builder\Aggregation\MaxAggregation;
use MongoDB\Builder\Aggregation\MaxNAggregation;
use MongoDB\Builder\Aggregation\MedianAggregation;
use MongoDB\Builder\Aggregation\MergeObjectsAggregation;
use MongoDB\Builder\Aggregation\MetaAggregation;
use MongoDB\Builder\Aggregation\MillisecondAggregation;
use MongoDB\Builder\Aggregation\MinAggregation;
use MongoDB\Builder\Aggregation\MinNAggregation;
use MongoDB\Builder\Aggregation\MinuteAggregation;
use MongoDB\Builder\Aggregation\ModAggregation;
use MongoDB\Builder\Aggregation\MonthAggregation;
use MongoDB\Builder\Aggregation\MultiplyAggregation;
use MongoDB\Builder\Aggregation\NeAggregation;
use MongoDB\Builder\Aggregation\NotAggregation;
use MongoDB\Builder\Aggregation\ObjectToArrayAggregation;
use MongoDB\Builder\Aggregation\OrAggregation;
use MongoDB\Builder\Aggregation\PercentileAggregation;
use MongoDB\Builder\Aggregation\PowAggregation;
use MongoDB\Builder\Aggregation\PushAggregation;
use MongoDB\Builder\Aggregation\RadiansToDegreesAggregation;
use MongoDB\Builder\Aggregation\RandAggregation;
use MongoDB\Builder\Aggregation\RangeAggregation;
use MongoDB\Builder\Aggregation\RankAggregation;
use MongoDB\Builder\Aggregation\ReduceAggregation;
use MongoDB\Builder\Aggregation\RegexFindAggregation;
use MongoDB\Builder\Aggregation\RegexFindAllAggregation;
use MongoDB\Builder\Aggregation\RegexMatchAggregation;
use MongoDB\Builder\Aggregation\ReplaceAllAggregation;
use MongoDB\Builder\Aggregation\ReplaceOneAggregation;
use MongoDB\Builder\Aggregation\ReverseArrayAggregation;
use MongoDB\Builder\Aggregation\RoundAggregation;
use MongoDB\Builder\Aggregation\RtrimAggregation;
use MongoDB\Builder\Aggregation\SampleRateAggregation;
use MongoDB\Builder\Aggregation\SecondAggregation;
use MongoDB\Builder\Aggregation\SetDifferenceAggregation;
use MongoDB\Builder\Aggregation\SetEqualsAggregation;
use MongoDB\Builder\Aggregation\SetFieldAggregation;
use MongoDB\Builder\Aggregation\SetIntersectionAggregation;
use MongoDB\Builder\Aggregation\SetIsSubsetAggregation;
use MongoDB\Builder\Aggregation\SetUnionAggregation;
use MongoDB\Builder\Aggregation\ShiftAggregation;
use MongoDB\Builder\Aggregation\SinAggregation;
use MongoDB\Builder\Aggregation\SinhAggregation;
use MongoDB\Builder\Aggregation\SizeAggregation;
use MongoDB\Builder\Aggregation\SliceAggregation;
use MongoDB\Builder\Aggregation\SortArrayAggregation;
use MongoDB\Builder\Aggregation\SplitAggregation;
use MongoDB\Builder\Aggregation\SqrtAggregation;
use MongoDB\Builder\Aggregation\StdDevPopAggregation;
use MongoDB\Builder\Aggregation\StdDevSampAggregation;
use MongoDB\Builder\Aggregation\StrLenBytesAggregation;
use MongoDB\Builder\Aggregation\StrLenCPAggregation;
use MongoDB\Builder\Aggregation\StrcasecmpAggregation;
use MongoDB\Builder\Aggregation\SubstrAggregation;
use MongoDB\Builder\Aggregation\SubstrBytesAggregation;
use MongoDB\Builder\Aggregation\SubstrCPAggregation;
use MongoDB\Builder\Aggregation\SubtractAggregation;
use MongoDB\Builder\Aggregation\SumAggregation;
use MongoDB\Builder\Aggregation\SwitchAggregation;
use MongoDB\Builder\Aggregation\TanAggregation;
use MongoDB\Builder\Aggregation\TanhAggregation;
use MongoDB\Builder\Aggregation\ToBoolAggregation;
use MongoDB\Builder\Aggregation\ToDateAggregation;
use MongoDB\Builder\Aggregation\ToDecimalAggregation;
use MongoDB\Builder\Aggregation\ToDoubleAggregation;
use MongoDB\Builder\Aggregation\ToIntAggregation;
use MongoDB\Builder\Aggregation\ToLongAggregation;
use MongoDB\Builder\Aggregation\ToLowerAggregation;
use MongoDB\Builder\Aggregation\ToObjectIdAggregation;
use MongoDB\Builder\Aggregation\ToStringAggregation;
use MongoDB\Builder\Aggregation\ToUpperAggregation;
use MongoDB\Builder\Aggregation\TopAggregation;
use MongoDB\Builder\Aggregation\TopNAggregation;
use MongoDB\Builder\Aggregation\TrimAggregation;
use MongoDB\Builder\Aggregation\TruncAggregation;
use MongoDB\Builder\Aggregation\TsIncrementAggregation;
use MongoDB\Builder\Aggregation\TsSecondAggregation;
use MongoDB\Builder\Aggregation\TypeAggregation;
use MongoDB\Builder\Aggregation\UnsetFieldAggregation;
use MongoDB\Builder\Aggregation\WeekAggregation;
use MongoDB\Builder\Aggregation\YearAggregation;
use MongoDB\Builder\Aggregation\ZipAggregation;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToBinary;
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToDecimal;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToFloat;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToNull;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Expression\ResolvesToObjectId;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Expression\ResolvesToTimestamp;
use MongoDB\Model\BSONArray;
use stdClass;

final class Aggregation
{
    /**
     * Returns the absolute value of a number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/abs/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $value
     */
    public static function abs(Decimal128|Int64|ResolvesToNumber|float|int $value): AbsAggregation
    {
        return new AbsAggregation($value);
    }

    /**
     * Defines a custom accumulator function.
     * New in version 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/accumulator/
     * @param non-empty-string $init Function used to initialize the state. The init function receives its arguments from the initArgs array expression. You can specify the function definition as either BSON type Code or String.
     * @param non-empty-string $accumulate Function used to accumulate documents. The accumulate function receives its arguments from the current state and accumulateArgs array expression. The result of the accumulate function becomes the new state. You can specify the function definition as either BSON type Code or String.
     * @param BSONArray|PackedArray|ResolvesToArray|list $accumulateArgs Arguments passed to the accumulate function. You can use accumulateArgs to specify what field value(s) to pass to the accumulate function.
     * @param non-empty-string $merge Function used to merge two internal states. merge must be either a String or Code BSON type. merge returns the combined result of the two merged states. For information on when the merge function is called, see Merge Two States with $merge.
     * @param non-empty-string $lang The language used in the $accumulator code.
     * @param BSONArray|Optional|PackedArray|ResolvesToArray|list $initArgs Arguments passed to the init function.
     * @param Optional|non-empty-string $finalize Function used to update the result of the accumulation.
     */
    public static function accumulator(
        string $init,
        string $accumulate,
        PackedArray|ResolvesToArray|BSONArray|array $accumulateArgs,
        string $merge,
        string $lang,
        PackedArray|ResolvesToArray|Optional|BSONArray|array $initArgs = Optional::Undefined,
        Optional|string $finalize = Optional::Undefined,
    ): AccumulatorAggregation
    {
        return new AccumulatorAggregation($init, $accumulate, $accumulateArgs, $merge, $lang, $initArgs, $finalize);
    }

    /**
     * Returns the inverse cosine (arc cosine) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acos/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $acos takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $acos returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acos returns values as a double. $acos can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function acos(Decimal128|Int64|ResolvesToNumber|float|int $expression): AcosAggregation
    {
        return new AcosAggregation($expression);
    }

    /**
     * Returns the inverse hyperbolic cosine (hyperbolic arc cosine) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acosh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $acosh takes any valid expression that resolves to a number between 1 and +Infinity, e.g. 1 <= value <= +Infinity.
     * $acosh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acosh returns values as a double. $acosh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function acosh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AcoshAggregation
    {
        return new AcoshAggregation($expression);
    }

    /**
     * Adds numbers to return the sum, or adds numbers and a date to return a new date. If adding numbers and a date, treats the numbers as milliseconds. Accepts any number of argument expressions, but at most, one expression can resolve to a date.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/add/
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int ...$expression The arguments can be any valid expression as long as they resolve to either all numbers or to numbers and a date.
     */
    public static function add(
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int ...$expression,
    ): AddAggregation
    {
        return new AddAggregation(...$expression);
    }

    /**
     * Returns an array of unique expression values for each group. Order of the array elements is undefined.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addToSet/
     * @param ExpressionInterface|FieldPath|mixed|non-empty-string $expression
     */
    public static function addToSet(mixed $expression): AddToSetAggregation
    {
        return new AddToSetAggregation($expression);
    }

    /**
     * Returns true if no element of a set evaluates to false, otherwise, returns false. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/allElementsTrue/
     * @param BSONArray|PackedArray|ResolvesToArray|list ...$expression
     */
    public static function allElementsTrue(
        PackedArray|ResolvesToArray|BSONArray|array ...$expression,
    ): AllElementsTrueAggregation
    {
        return new AllElementsTrueAggregation(...$expression);
    }

    /**
     * Returns true only when all its expressions evaluate to true. Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/and/
     * @param Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|bool|float|int|mixed|non-empty-string|null ...$expression
     */
    public static function and(mixed ...$expression): AndAggregation
    {
        return new AndAggregation(...$expression);
    }

    /**
     * Returns true if any elements of a set evaluate to true; otherwise, returns false. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/anyElementTrue/
     * @param BSONArray|PackedArray|ResolvesToArray|list $expression
     */
    public static function anyElementTrue(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
    ): AnyElementTrueAggregation
    {
        return new AnyElementTrueAggregation($expression);
    }

    /**
     * Returns the element at the specified array index.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayElemAt/
     * @param BSONArray|PackedArray|ResolvesToArray|list $array
     * @param Int64|ResolvesToInt|int $idx
     */
    public static function arrayElemAt(
        PackedArray|ResolvesToArray|BSONArray|array $array,
        Int64|ResolvesToInt|int $idx,
    ): ArrayElemAtAggregation
    {
        return new ArrayElemAtAggregation($array, $idx);
    }

    /**
     * Converts an array of key value pairs to a document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/
     * @param BSONArray|PackedArray|ResolvesToArray|list $array
     */
    public static function arrayToObject(PackedArray|ResolvesToArray|BSONArray|array $array): ArrayToObjectAggregation
    {
        return new ArrayToObjectAggregation($array);
    }

    /**
     * Returns the inverse sin (arc sine) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asin/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $asin takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $asin returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asin returns values as a double. $asin can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function asin(Decimal128|Int64|ResolvesToNumber|float|int $expression): AsinAggregation
    {
        return new AsinAggregation($expression);
    }

    /**
     * Returns the inverse hyperbolic sine (hyperbolic arc sine) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asinh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function asinh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AsinhAggregation
    {
        return new AsinhAggregation($expression);
    }

    /**
     * Returns the inverse tangent (arc tangent) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atan/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $atan takes any valid expression that resolves to a number.
     * $atan returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atan returns values as a double. $atan can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function atan(Decimal128|Int64|ResolvesToNumber|float|int $expression): AtanAggregation
    {
        return new AtanAggregation($expression);
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
    ): Atan2Aggregation
    {
        return new Atan2Aggregation($y, $x);
    }

    /**
     * Returns the inverse hyperbolic tangent (hyperbolic arc tangent) of a value in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atanh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $atanh takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $atanh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atanh returns values as a double. $atanh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function atanh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AtanhAggregation
    {
        return new AtanhAggregation($expression);
    }

    /**
     * Returns an average of numerical values. Ignores non-numeric values.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function avg(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): AvgAggregation
    {
        return new AvgAggregation(...$expression);
    }

    /**
     * Returns the size of a given string or binary data value's content in bytes.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/binarySize/
     * @param Binary|ResolvesToBinary|ResolvesToNull|ResolvesToString|non-empty-string|null $expression
     */
    public static function binarySize(
        Binary|ResolvesToBinary|ResolvesToNull|ResolvesToString|null|string $expression,
    ): BinarySizeAggregation
    {
        return new BinarySizeAggregation($expression);
    }

    /**
     * Returns the result of a bitwise and operation on an array of int or long values.
     * New in version 6.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitAnd/
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public static function bitAnd(Int64|ResolvesToInt|ResolvesToLong|int ...$expression): BitAndAggregation
    {
        return new BitAndAggregation(...$expression);
    }

    /**
     * Returns the result of a bitwise not operation on a single argument or an array that contains a single int or long value.
     * New in version 6.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitNot/
     * @param Int64|ResolvesToInt|ResolvesToLong|int $expression
     */
    public static function bitNot(Int64|ResolvesToInt|ResolvesToLong|int $expression): BitNotAggregation
    {
        return new BitNotAggregation($expression);
    }

    /**
     * Returns the result of a bitwise or operation on an array of int or long values.
     * New in version 6.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitOr/
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public static function bitOr(Int64|ResolvesToInt|ResolvesToLong|int ...$expression): BitOrAggregation
    {
        return new BitOrAggregation(...$expression);
    }

    /**
     * Returns the result of a bitwise xor (exclusive or) operation on an array of int and long values.
     * New in version 6.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitXor/
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public static function bitXor(Int64|ResolvesToInt|ResolvesToLong|int ...$expression): BitXorAggregation
    {
        return new BitXorAggregation(...$expression);
    }

    /**
     * Returns the bottom element within a group according to the specified sort order.
     * New in version 5.2.
     *
     * Available in the $group and $setWindowFields stages.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom/
     * @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public static function bottom(stdClass|array $sortBy, mixed $output): BottomAggregation
    {
        return new BottomAggregation($sortBy, $output);
    }

    /**
     * Returns an aggregation of the bottom n elements within a group, according to the specified sort order. If the group contains fewer than n elements, $bottomN returns all elements in the group.
     * New in version 5.2.
     * Available in the $group and $setWindowFields stages.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/
     * @param Int64|ResolvesToInt|int $n Limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group.
     * @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public static function bottomN(
        Int64|ResolvesToInt|int $n,
        stdClass|array $sortBy,
        mixed $output,
    ): BottomNAggregation
    {
        return new BottomNAggregation($n, $sortBy, $output);
    }

    /**
     * Returns the size in bytes of a given document (i.e. bsontype Object) when encoded as BSON.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/
     * @param Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|stdClass $object
     */
    public static function bsonSize(
        Document|Serializable|ResolvesToNull|ResolvesToObject|stdClass|array|null $object,
    ): BsonSizeAggregation
    {
        return new BsonSizeAggregation($object);
    }

    /**
     * Returns the smallest integer greater than or equal to the specified number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ceil/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression If the argument resolves to a value of null or refers to a field that is missing, $ceil returns null. If the argument resolves to NaN, $ceil returns NaN.
     */
    public static function ceil(Decimal128|Int64|ResolvesToNumber|float|int $expression): CeilAggregation
    {
        return new CeilAggregation($expression);
    }

    /**
     * Returns 0 if the two values are equivalent, 1 if the first value is greater than the second, and -1 if the first value is less than the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cmp/
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function cmp(mixed $expression1, mixed $expression2): CmpAggregation
    {
        return new CmpAggregation($expression1, $expression2);
    }

    /**
     * Concatenates any number of strings.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concat/
     * @param ResolvesToString|non-empty-string ...$expression
     */
    public static function concat(ResolvesToString|string ...$expression): ConcatAggregation
    {
        return new ConcatAggregation(...$expression);
    }

    /**
     * Concatenates arrays to return the concatenated array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concatArrays/
     * @param BSONArray|PackedArray|ResolvesToArray|list ...$array
     */
    public static function concatArrays(
        PackedArray|ResolvesToArray|BSONArray|array ...$array,
    ): ConcatArraysAggregation
    {
        return new ConcatArraysAggregation(...$array);
    }

    /**
     * A ternary operator that evaluates one expression, and depending on the result, returns the value of one of the other two expressions. Accepts either three expressions in an ordered list or three named parameters.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cond/
     * @param ResolvesToBool|bool $if
     * @param ExpressionInterface|mixed $then
     * @param ExpressionInterface|mixed $else
     */
    public static function cond(ResolvesToBool|bool $if, mixed $then, mixed $else): CondAggregation
    {
        return new CondAggregation($if, $then, $else);
    }

    /**
     * Converts a value to a specified type.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/
     * @param ExpressionInterface|mixed $input
     * @param Int64|ResolvesToInt|ResolvesToString|int|non-empty-string $to
     * @param ExpressionInterface|Optional|mixed $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     * @param ExpressionInterface|Optional|mixed $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public static function convert(
        mixed $input,
        Int64|ResolvesToInt|ResolvesToString|int|string $to,
        mixed $onError = Optional::Undefined,
        mixed $onNull = Optional::Undefined,
    ): ConvertAggregation
    {
        return new ConvertAggregation($input, $to, $onError, $onNull);
    }

    /**
     * Returns the cosine of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cos/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $cos takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $cos returns values as a double. $cos can also return values as a 128-bit decimal as long as the <expression> resolves to a 128-bit decimal value.
     */
    public static function cos(Decimal128|Int64|ResolvesToNumber|float|int $expression): CosAggregation
    {
        return new CosAggregation($expression);
    }

    /**
     * Returns the hyperbolic cosine of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cosh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $cosh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $cosh returns values as a double. $cosh can also return values as a 128-bit decimal if the <expression> resolves to a 128-bit decimal value.
     */
    public static function cosh(Decimal128|Int64|ResolvesToNumber|float|int $expression): CoshAggregation
    {
        return new CoshAggregation($expression);
    }

    /**
     * Returns the number of documents in the group or window.
     * Distinct from the $count pipeline stage.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count/
     * @param non-empty-string $field
     */
    public static function count(string $field): CountAggregation
    {
        return new CountAggregation($field);
    }

    /**
     * Returns the population covariance of two numeric expressions.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covariancePop/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression1
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression2
     */
    public static function covariancePop(
        Decimal128|Int64|ResolvesToNumber|float|int $expression1,
        Decimal128|Int64|ResolvesToNumber|float|int $expression2,
    ): CovariancePopAggregation
    {
        return new CovariancePopAggregation($expression1, $expression2);
    }

    /**
     * Returns the sample covariance of two numeric expressions.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covarianceSamp/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression1
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression2
     */
    public static function covarianceSamp(
        Decimal128|Int64|ResolvesToNumber|float|int $expression1,
        Decimal128|Int64|ResolvesToNumber|float|int $expression2,
    ): CovarianceSampAggregation
    {
        return new CovarianceSampAggregation($expression1, $expression2);
    }

    /**
     * Adds a number of time units to a date object.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The unit used to measure the amount of time added to the startDate.
     * @param Int64|ResolvesToInt|ResolvesToLong|int $amount
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dateAdd(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ResolvesToString|string $unit,
        Int64|ResolvesToInt|ResolvesToLong|int $amount,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DateAddAggregation
    {
        return new DateAddAggregation($startDate, $unit, $amount, $timezone);
    }

    /**
     * Returns the difference between two dates.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $startDate The start of the time period. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $endDate The end of the time period. The endDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The time measurement unit between the startDate and endDate
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|ResolvesToString|non-empty-string $startOfWeek Used when the unit is equal to week. Defaults to Sunday. The startOfWeek parameter is an expression that resolves to a case insensitive string
     */
    public static function dateDiff(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $endDate,
        ResolvesToString|string $unit,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        ResolvesToString|Optional|string $startOfWeek = Optional::Undefined,
    ): DateDiffAggregation
    {
        return new DateDiffAggregation($startDate, $endDate, $unit, $timezone, $startOfWeek);
    }

    /**
     * Constructs a BSON Date object given the date's constituent parts.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromParts/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $year Calendar year. Can be any expression that evaluates to a number.
     * @param Decimal128|Int64|ResolvesToNumber|float|int $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $month Month. Defaults to 1.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $isoWeek Week of year. Defaults to 1.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $day Day of month. Defaults to 1.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $hour Hour. Defaults to 0.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $minute Minute. Defaults to 0.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $second Second. Defaults to 0.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $millisecond Millisecond. Defaults to 0.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dateFromParts(
        Decimal128|Int64|ResolvesToNumber|float|int $year,
        Decimal128|Int64|ResolvesToNumber|float|int $isoWeekYear,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $month = Optional::Undefined,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $isoWeek = Optional::Undefined,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $day = Optional::Undefined,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $isoDayOfWeek = Optional::Undefined,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $hour = Optional::Undefined,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $minute = Optional::Undefined,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $second = Optional::Undefined,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $millisecond = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DateFromPartsAggregation
    {
        return new DateFromPartsAggregation($year, $isoWeekYear, $month, $isoWeek, $day, $isoDayOfWeek, $hour, $minute, $second, $millisecond, $timezone);
    }

    /**
     * Converts a date/time string to a date object.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/
     * @param ResolvesToString|non-empty-string $dateString The date/time string to convert to a date object.
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     * @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date.
     * @param ExpressionInterface|Optional|mixed $onError If $dateFromString encounters an error while parsing the given dateString, it outputs the result value of the provided onError expression. This result value can be of any type.
     * If you do not specify onError, $dateFromString throws an error if it cannot parse dateString.
     * @param ExpressionInterface|Optional|mixed $onNull If the dateString provided to $dateFromString is null or missing, it outputs the result value of the provided onNull expression. This result value can be of any type.
     * If you do not specify onNull and dateString is null or missing, then $dateFromString outputs null.
     */
    public static function dateFromString(
        ResolvesToString|string $dateString,
        ResolvesToString|Optional|string $format = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        mixed $onError = Optional::Undefined,
        mixed $onNull = Optional::Undefined,
    ): DateFromStringAggregation
    {
        return new DateFromStringAggregation($dateString, $format, $timezone, $onError, $onNull);
    }

    /**
     * Subtracts a number of time units from a date object.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The unit used to measure the amount of time added to the startDate.
     * @param Int64|ResolvesToInt|ResolvesToLong|int $amount
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dateSubtract(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ResolvesToString|string $unit,
        Int64|ResolvesToInt|ResolvesToLong|int $amount,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DateSubtractAggregation
    {
        return new DateSubtractAggregation($startDate, $unit, $amount, $timezone);
    }

    /**
     * Returns a document containing the constituent parts of a date.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToParts/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The input date for which to return parts. date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|bool $iso8601 If set to true, modifies the output document to use ISO week date fields. Defaults to false.
     */
    public static function dateToParts(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        Optional|bool $iso8601 = Optional::Undefined,
    ): DateToPartsAggregation
    {
        return new DateToPartsAggregation($date, $timezone, $iso8601);
    }

    /**
     * Returns the date as a formatted string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToString/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to convert to string. Must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     * @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date.
     * @param ExpressionInterface|Optional|mixed $onNull The value to return if the date is null or missing.
     * If unspecified, $dateToString returns null if the date is null or missing.
     */
    public static function dateToString(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $format = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        mixed $onNull = Optional::Undefined,
    ): DateToStringAggregation
    {
        return new DateToStringAggregation($date, $format, $timezone, $onNull);
    }

    /**
     * Truncates a date.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateTrunc/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to truncate, specified in UTC. The date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The unit of time, specified as an expression that must resolve to one of these strings: year, quarter, week, month, day, hour, minute, second.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     * @param Decimal128|Int64|Optional|ResolvesToNumber|float|int $binSize The numeric time value, specified as an expression that must resolve to a positive non-zero number. Defaults to 1.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|non-empty-string $startOfWeek The start of the week. Used when
     * unit is week. Defaults to Sunday.
     */
    public static function dateTrunc(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|string $unit,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $binSize = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        Optional|string $startOfWeek = Optional::Undefined,
    ): DateTruncAggregation
    {
        return new DateTruncAggregation($date, $unit, $binSize, $timezone, $startOfWeek);
    }

    /**
     * Returns the day of the month for a date as a number between 1 and 31.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfMonth/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfMonth(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DayOfMonthAggregation
    {
        return new DayOfMonthAggregation($date, $timezone);
    }

    /**
     * Returns the day of the week for a date as a number between 1 (Sunday) and 7 (Saturday).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfWeek/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfWeek(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DayOfWeekAggregation
    {
        return new DayOfWeekAggregation($date, $timezone);
    }

    /**
     * Returns the day of the year for a date as a number between 1 and 366 (leap year).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfYear/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfYear(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DayOfYearAggregation
    {
        return new DayOfYearAggregation($date, $timezone);
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
    ): DegreesToRadiansAggregation
    {
        return new DegreesToRadiansAggregation($expression);
    }

    /**
     * Returns the document position (known as the rank) relative to other documents in the $setWindowFields stage partition. There are no gaps in the ranks. Ties receive the same rank.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/denseRank/
     */
    public static function denseRank(): DenseRankAggregation
    {
        return new DenseRankAggregation();
    }

    /**
     * Returns the average rate of change within the specified window.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/derivative/
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public static function derivative(
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $input,
        Optional|string $unit = Optional::Undefined,
    ): DerivativeAggregation
    {
        return new DerivativeAggregation($input, $unit);
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
    ): DivideAggregation
    {
        return new DivideAggregation($dividend, $divisor);
    }

    /**
     * Returns the position of a document (known as the document number) in the $setWindowFields stage partition. Ties result in different adjacent document numbers.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documentNumber/
     */
    public static function documentNumber(): DocumentNumberAggregation
    {
        return new DocumentNumberAggregation();
    }

    /**
     * Returns true if the values are equivalent.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/eq/
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function eq(mixed $expression1, mixed $expression2): EqAggregation
    {
        return new EqAggregation($expression1, $expression2);
    }

    /**
     * Raises e to the specified exponent.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/exp/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $exponent
     */
    public static function exp(Decimal128|Int64|ResolvesToNumber|float|int $exponent): ExpAggregation
    {
        return new ExpAggregation($exponent);
    }

    /**
     * Returns the exponential moving average for the numeric expression.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/expMovingAvg/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $input
     * @param Int64|Optional|int $N An integer that specifies the number of historical documents that have a significant mathematical weight in the exponential moving average calculation, with the most recent documents contributing the most weight.
     * You must specify either N or alpha. You cannot specify both.
     * The N value is used in this formula to calculate the current result based on the expression value from the current document being read and the previous result of the calculation:
     * @param Int64|Optional|float|int $alpha A double that specifies the exponential decay value to use in the exponential moving average calculation. A higher alpha value assigns a lower mathematical significance to previous results from the calculation.
     * You must specify either N or alpha. You cannot specify both.
     */
    public static function expMovingAvg(
        Decimal128|Int64|ResolvesToNumber|float|int $input,
        Int64|Optional|int $N = Optional::Undefined,
        Int64|Optional|float|int $alpha = Optional::Undefined,
    ): ExpMovingAvgAggregation
    {
        return new ExpMovingAvgAggregation($input, $N, $alpha);
    }

    /**
     * Selects a subset of the array to return an array with only the elements that match the filter condition.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/
     * @param BSONArray|PackedArray|ResolvesToArray|list $input
     * @param ResolvesToBool|bool $cond An expression that resolves to a boolean value used to determine if an element should be included in the output array. The expression references each element of the input array individually with the variable name specified in as.
     * @param Optional|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     * @param Int64|Optional|ResolvesToInt|int $limit A number expression that restricts the number of matching array elements that $filter returns. You cannot specify a limit less than 1. The matching array elements are returned in the order they appear in the input array.
     * If the specified limit is greater than the number of matching array elements, $filter returns all matching array elements. If the limit is null, $filter returns all matching array elements.
     */
    public static function filter(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToBool|bool $cond,
        Optional|string $as = Optional::Undefined,
        Int64|ResolvesToInt|Optional|int $limit = Optional::Undefined,
    ): FilterAggregation
    {
        return new FilterAggregation($input, $cond, $as, $limit);
    }

    /**
     * Returns the result of an expression for the first document in a group or window.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/first/
     * @param ExpressionInterface|mixed $expression
     */
    public static function first(mixed $expression): FirstAggregation
    {
        return new FirstAggregation($expression);
    }

    /**
     * Returns a specified number of elements from the beginning of an array. Distinct from the $firstN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN-array-element/
     * @param BSONArray|PackedArray|ResolvesToArray|list $input An expression that resolves to the array from which to return n elements.
     * @param Int64|ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     */
    public static function firstN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Int64|ResolvesToInt|int $n,
    ): FirstNAggregation
    {
        return new FirstNAggregation($input, $n);
    }

    /**
     * Returns the largest integer less than or equal to the specified number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/floor/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public static function floor(Decimal128|Int64|ResolvesToNumber|float|int $expression): FloorAggregation
    {
        return new FloorAggregation($expression);
    }

    /**
     * Defines a custom function.
     * New in version 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/function/
     * @param non-empty-string $body The function definition. You can specify the function definition as either BSON type Code or String.
     * @param BSONArray|PackedArray|list $args Arguments passed to the function body. If the body function does not take an argument, you can specify an empty array [ ].
     * @param non-empty-string $lang
     */
    public static function function(
        string $body,
        PackedArray|BSONArray|array $args,
        string $lang,
    ): FunctionAggregation
    {
        return new FunctionAggregation($body, $args, $lang);
    }

    /**
     * Returns the value of a specified field from a document. You can use $getField to retrieve the value of fields with names that contain periods (.) or start with dollar signs ($).
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/
     * @param non-empty-string $field Field in the input object for which you want to return a value. field can be any valid expression that resolves to a string constant.
     * If field begins with a dollar sign ($), place the field name inside of a $literal expression to return its value.
     * @param ExpressionInterface|Optional|mixed $input Default: $$CURRENT
     * A valid expression that contains the field for which you want to return a value. input must resolve to an object, missing, null, or undefined. If omitted, defaults to the document currently being processed in the pipeline ($$CURRENT).
     */
    public static function getField(string $field, mixed $input = Optional::Undefined): GetFieldAggregation
    {
        return new GetFieldAggregation($field, $input);
    }

    /**
     * Returns true if the first value is greater than the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gt/
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function gt(mixed $expression1, mixed $expression2): GtAggregation
    {
        return new GtAggregation($expression1, $expression2);
    }

    /**
     * Returns true if the first value is greater than or equal to the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gte/
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function gte(mixed $expression1, mixed $expression2): GteAggregation
    {
        return new GteAggregation($expression1, $expression2);
    }

    /**
     * Returns the hour for a date as a number between 0 and 23.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hour/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function hour(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): HourAggregation
    {
        return new HourAggregation($date, $timezone);
    }

    /**
     * Returns either the non-null result of the first expression or the result of the second expression if the first expression results in a null result. Null result encompasses instances of undefined values or missing fields. Accepts two expressions as arguments. The result of the second expression can be null.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ifNull/
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function ifNull(mixed ...$expression): IfNullAggregation
    {
        return new IfNullAggregation(...$expression);
    }

    /**
     * Returns a boolean indicating whether a specified value is in an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/in/
     * @param ExpressionInterface|mixed $expression Any valid expression expression.
     * @param BSONArray|PackedArray|ResolvesToArray|list $array Any valid expression that resolves to an array.
     */
    public static function in(mixed $expression, PackedArray|ResolvesToArray|BSONArray|array $array): InAggregation
    {
        return new InAggregation($expression, $array);
    }

    /**
     * Searches an array for an occurrence of a specified value and returns the array index of the first occurrence. Array indexes start at zero.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfArray/
     * @param ResolvesToString|non-empty-string $array Can be any valid expression as long as it resolves to an array.
     * If the array expression resolves to a value of null or refers to a field that is missing, $indexOfArray returns null.
     * If the array expression does not resolve to an array or null nor refers to a missing field, $indexOfArray returns an error.
     * @param ExpressionInterface|mixed $search
     * @param Int64|Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Int64|Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public static function indexOfArray(
        ResolvesToString|string $array,
        mixed $search,
        Int64|ResolvesToInt|Optional|int $start = Optional::Undefined,
        Int64|ResolvesToInt|Optional|int $end = Optional::Undefined,
    ): IndexOfArrayAggregation
    {
        return new IndexOfArrayAggregation($array, $search, $start, $end);
    }

    /**
     * Searches a string for an occurrence of a substring and returns the UTF-8 byte index of the first occurrence. If the substring is not found, returns -1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfBytes/
     * @param ResolvesToString|non-empty-string $string Can be any valid expression as long as it resolves to a string.
     * If the string expression resolves to a value of null or refers to a field that is missing, $indexOfBytes returns null.
     * If the string expression does not resolve to a string or null nor refers to a missing field, $indexOfBytes returns an error.
     * @param ResolvesToString|non-empty-string $substring Can be any valid expression as long as it resolves to a string.
     * @param Int64|Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Int64|Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public static function indexOfBytes(
        ResolvesToString|string $string,
        ResolvesToString|string $substring,
        Int64|ResolvesToInt|Optional|int $start = Optional::Undefined,
        Int64|ResolvesToInt|Optional|int $end = Optional::Undefined,
    ): IndexOfBytesAggregation
    {
        return new IndexOfBytesAggregation($string, $substring, $start, $end);
    }

    /**
     * Searches a string for an occurrence of a substring and returns the UTF-8 code point index of the first occurrence. If the substring is not found, returns -1
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfCP/
     * @param ResolvesToString|non-empty-string $string Can be any valid expression as long as it resolves to a string.
     * If the string expression resolves to a value of null or refers to a field that is missing, $indexOfCP returns null.
     * If the string expression does not resolve to a string or null nor refers to a missing field, $indexOfCP returns an error.
     * @param ResolvesToString|non-empty-string $substring Can be any valid expression as long as it resolves to a string.
     * @param Int64|Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Int64|Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public static function indexOfCP(
        ResolvesToString|string $string,
        ResolvesToString|string $substring,
        Int64|ResolvesToInt|Optional|int $start = Optional::Undefined,
        Int64|ResolvesToInt|Optional|int $end = Optional::Undefined,
    ): IndexOfCPAggregation
    {
        return new IndexOfCPAggregation($string, $substring, $start, $end);
    }

    /**
     * Returns the approximation of the area under a curve.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/integral/
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|ResolvesToString|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public static function integral(
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $input,
        ResolvesToString|Optional|string $unit = Optional::Undefined,
    ): IntegralAggregation
    {
        return new IntegralAggregation($input, $unit);
    }

    /**
     * Determines if the operand is an array. Returns a boolean.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isArray/
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function isArray(mixed ...$expression): IsArrayAggregation
    {
        return new IsArrayAggregation(...$expression);
    }

    /**
     * Returns boolean true if the specified expression resolves to an integer, decimal, double, or long.
     * Returns boolean false if the expression resolves to any other BSON type, null, or a missing field.
     * New in version 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isNumber/
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function isNumber(mixed ...$expression): IsNumberAggregation
    {
        return new IsNumberAggregation(...$expression);
    }

    /**
     * Returns the weekday number in ISO 8601 format, ranging from 1 (for Monday) to 7 (for Sunday).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoDayOfWeek/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoDayOfWeek(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): IsoDayOfWeekAggregation
    {
        return new IsoDayOfWeekAggregation($date, $timezone);
    }

    /**
     * Returns the week number in ISO 8601 format, ranging from 1 to 53. Week numbers start at 1 with the week (Monday through Sunday) that contains the year's first Thursday.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoWeek/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoWeek(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): IsoWeekAggregation
    {
        return new IsoWeekAggregation($date, $timezone);
    }

    /**
     * Returns the year number in ISO 8601 format. The year starts with the Monday of week 1 (ISO 8601) and ends with the Sunday of the last week (ISO 8601).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoWeekYear/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoWeekYear(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): IsoWeekYearAggregation
    {
        return new IsoWeekYearAggregation($date, $timezone);
    }

    /**
     * Returns the result of an expression for the last document in a group or window.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/
     * @param ExpressionInterface|mixed $expression
     */
    public static function last(mixed $expression): LastAggregation
    {
        return new LastAggregation($expression);
    }

    /**
     * Returns a specified number of elements from the end of an array. Distinct from the $lastN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/
     * @param BSONArray|PackedArray|ResolvesToArray|list $input An expression that resolves to the array from which to return n elements.
     * @param Int64|ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     */
    public static function lastN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Int64|ResolvesToInt|int $n,
    ): LastNAggregation
    {
        return new LastNAggregation($input, $n);
    }

    /**
     * Defines variables for use within the scope of a subexpression and returns the result of the subexpression. Accepts named parameters.
     * Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/let/
     * @param Document|Serializable|array|stdClass $vars Assignment block for the variables accessible in the in expression. To assign a variable, specify a string for the variable name and assign a valid expression for the value.
     * The variable assignments have no meaning outside the in expression, not even within the vars block itself.
     * @param ExpressionInterface|mixed $in The expression to evaluate.
     */
    public static function let(Document|Serializable|stdClass|array $vars, mixed $in): LetAggregation
    {
        return new LetAggregation($vars, $in);
    }

    /**
     * Fills null and missing fields in a window using linear interpolation based on surrounding field values.
     * Available in the $setWindowFields stage.
     * New in version 5.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/linearFill/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public static function linearFill(Decimal128|Int64|ResolvesToNumber|float|int $expression): LinearFillAggregation
    {
        return new LinearFillAggregation($expression);
    }

    /**
     * Return a value without parsing. Use for values that the aggregation pipeline may interpret as an expression. For example, use a $literal expression to a string that starts with a dollar sign ($) to avoid parsing as a field path.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/literal/
     * @param mixed $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression.
     */
    public static function literal(mixed $value): LiteralAggregation
    {
        return new LiteralAggregation($value);
    }

    /**
     * Calculates the natural log of a number.
     * $ln is equivalent to $log: [ <number>, Math.E ] expression, where Math.E is a JavaScript representation for Euler's number e.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ln/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number. For more information on expressions, see Expressions.
     */
    public static function ln(Decimal128|Int64|ResolvesToNumber|float|int $number): LnAggregation
    {
        return new LnAggregation($number);
    }

    /**
     * Last observation carried forward. Sets values for null and missing fields in a window to the last non-null value for the field.
     * Available in the $setWindowFields stage.
     * New in version 5.2.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/locf/
     * @param ExpressionInterface|mixed $expression
     */
    public static function locf(mixed $expression): LocfAggregation
    {
        return new LocfAggregation($expression);
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
    ): LogAggregation
    {
        return new LogAggregation($number, $base);
    }

    /**
     * Calculates the log base 10 of a number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log10/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number.
     */
    public static function log10(Decimal128|Int64|ResolvesToNumber|float|int $number): Log10Aggregation
    {
        return new Log10Aggregation($number);
    }

    /**
     * Returns true if the first value is less than the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lt/
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function lt(mixed $expression1, mixed $expression2): LtAggregation
    {
        return new LtAggregation($expression1, $expression2);
    }

    /**
     * Returns true if the first value is less than or equal to the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lte/
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function lte(mixed $expression1, mixed $expression2): LteAggregation
    {
        return new LteAggregation($expression1, $expression2);
    }

    /**
     * Removes whitespace or the specified characters from the beginning of a string.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ltrim/
     * @param ResolvesToString|non-empty-string $input The string to trim. The argument can be any valid expression that resolves to a string.
     * @param Optional|ResolvesToString|non-empty-string $chars The character(s) to trim from the beginning of the input.
     * The argument can be any valid expression that resolves to a string. The $ltrim operator breaks down the string into individual UTF code point to trim from input.
     * If unspecified, $ltrim removes whitespace characters, including the null character.
     */
    public static function ltrim(
        ResolvesToString|string $input,
        ResolvesToString|Optional|string $chars = Optional::Undefined,
    ): LtrimAggregation
    {
        return new LtrimAggregation($input, $chars);
    }

    /**
     * Applies a subexpression to each element of an array and returns the array of resulting values in order. Accepts named parameters.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/
     * @param BSONArray|PackedArray|ResolvesToArray|list $input An expression that resolves to an array.
     * @param ExpressionInterface|mixed $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as.
     * @param Optional|ResolvesToString|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     */
    public static function map(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        mixed $in,
        ResolvesToString|Optional|string $as = Optional::Undefined,
    ): MapAggregation
    {
        return new MapAggregation($input, $in, $as);
    }

    /**
     * Returns the maximum value that results from applying an expression to each document.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function max(mixed ...$expression): MaxAggregation
    {
        return new MaxAggregation(...$expression);
    }

    /**
     * Returns the n largest values in an array. Distinct from the $maxN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN-array-element/
     * @param BSONArray|PackedArray|ResolvesToArray|list $input An expression that resolves to the array from which to return the maximal n elements.
     * @param Int64|ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns.
     */
    public static function maxN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Int64|ResolvesToInt|int $n,
    ): MaxNAggregation
    {
        return new MaxNAggregation($input, $n);
    }

    /**
     * Returns an approximation of the median, the 50th percentile, as a scalar value.
     * New in version 7.0.
     * This operator is available as an accumulator in these stages:
     * $group
     * $setWindowFields
     * It is also available as an aggregation expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $input $median calculates the 50th percentile value of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $median calculation ignores it.
     * @param non-empty-string $method The method that mongod uses to calculate the 50th percentile value. The method must be 'approximate'.
     */
    public static function median(
        Decimal128|Int64|ResolvesToNumber|float|int $input,
        string $method,
    ): MedianAggregation
    {
        return new MedianAggregation($input, $method);
    }

    /**
     * Combines multiple documents into a single document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/
     * @param Document|ResolvesToObject|Serializable|array|stdClass ...$document Any valid expression that resolves to a document.
     */
    public static function mergeObjects(
        Document|Serializable|ResolvesToObject|stdClass|array ...$document,
    ): MergeObjectsAggregation
    {
        return new MergeObjectsAggregation(...$document);
    }

    /**
     * Access available per-document metadata related to the aggregation operation.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/
     * @param non-empty-string $keyword
     */
    public static function meta(string $keyword): MetaAggregation
    {
        return new MetaAggregation($keyword);
    }

    /**
     * Returns the milliseconds of a date as a number between 0 and 999.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/millisecond/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function millisecond(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): MillisecondAggregation
    {
        return new MillisecondAggregation($date, $timezone);
    }

    /**
     * Returns the minimum value that results from applying an expression to each document.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function min(mixed ...$expression): MinAggregation
    {
        return new MinAggregation(...$expression);
    }

    /**
     * Returns the n smallest values in an array. Distinct from the $minN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN-array-element/
     * @param BSONArray|PackedArray|ResolvesToArray|list $input An expression that resolves to the array from which to return the maximal n elements.
     * @param Int64|ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns.
     */
    public static function minN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Int64|ResolvesToInt|int $n,
    ): MinNAggregation
    {
        return new MinNAggregation($input, $n);
    }

    /**
     * Returns the minute for a date as a number between 0 and 59.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minute/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function minute(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): MinuteAggregation
    {
        return new MinuteAggregation($date, $timezone);
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
    ): ModAggregation
    {
        return new ModAggregation($dividend, $divisor);
    }

    /**
     * Returns the month for a date as a number between 1 (January) and 12 (December).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/month/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function month(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): MonthAggregation
    {
        return new MonthAggregation($date, $timezone);
    }

    /**
     * Multiplies numbers to return the product. Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/multiply/
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression The arguments can be any valid expression as long as they resolve to numbers.
     * Starting in MongoDB 6.1 you can optimize the $multiply operation. To improve performance, group references at the end of the argument list.
     */
    public static function multiply(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): MultiplyAggregation
    {
        return new MultiplyAggregation(...$expression);
    }

    /**
     * Returns true if the values are not equivalent.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ne/
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function ne(mixed $expression1, mixed $expression2): NeAggregation
    {
        return new NeAggregation($expression1, $expression2);
    }

    /**
     * Returns the boolean value that is the opposite of its argument expression. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/not/
     * @param ExpressionInterface|ResolvesToBool|bool|mixed $expression
     */
    public static function not(mixed $expression): NotAggregation
    {
        return new NotAggregation($expression);
    }

    /**
     * Converts a document to an array of documents representing key-value pairs.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/
     * @param Document|ResolvesToObject|Serializable|array|stdClass $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields.
     */
    public static function objectToArray(
        Document|Serializable|ResolvesToObject|stdClass|array $object,
    ): ObjectToArrayAggregation
    {
        return new ObjectToArrayAggregation($object);
    }

    /**
     * Returns true when any of its expressions evaluates to true. Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/or/
     * @param ExpressionInterface|ResolvesToBool|bool|mixed ...$expression
     */
    public static function or(mixed ...$expression): OrAggregation
    {
        return new OrAggregation(...$expression);
    }

    /**
     * Returns an array of scalar values that correspond to specified percentile values.
     * New in version 7.0.
     *
     * This operator is available as an accumulator in these stages:
     * $group
     *
     * $setWindowFields
     *
     * It is also available as an aggregation expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $input $percentile calculates the percentile values of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $percentile calculation ignores it.
     * @param BSONArray|PackedArray|ResolvesToArray|list $p $percentile calculates a percentile value for each element in p. The elements represent percentages and must evaluate to numeric values in the range 0.0 to 1.0, inclusive.
     * $percentile returns results in the same order as the elements in p.
     * @param non-empty-string $method The method that mongod uses to calculate the percentile value. The method must be 'approximate'.
     */
    public static function percentile(
        Decimal128|Int64|ResolvesToNumber|float|int $input,
        PackedArray|ResolvesToArray|BSONArray|array $p,
        string $method,
    ): PercentileAggregation
    {
        return new PercentileAggregation($input, $p, $method);
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
    ): PowAggregation
    {
        return new PowAggregation($number, $exponent);
    }

    /**
     * Returns an array of values that result from applying an expression to each document.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/push/
     * @param ExpressionInterface|mixed $expression
     */
    public static function push(mixed $expression): PushAggregation
    {
        return new PushAggregation($expression);
    }

    /**
     * Converts a value from radians to degrees.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/radiansToDegrees/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public static function radiansToDegrees(
        Decimal128|Int64|ResolvesToNumber|float|int $expression,
    ): RadiansToDegreesAggregation
    {
        return new RadiansToDegreesAggregation($expression);
    }

    /**
     * Returns a random float between 0 and 1
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rand/
     */
    public static function rand(): RandAggregation
    {
        return new RandAggregation();
    }

    /**
     * Outputs an array containing a sequence of integers according to user-defined inputs.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/range/
     * @param Int64|ResolvesToInt|int $start An integer that specifies the start of the sequence. Can be any valid expression that resolves to an integer.
     * @param Int64|ResolvesToInt|int $end An integer that specifies the exclusive upper limit of the sequence. Can be any valid expression that resolves to an integer.
     * @param Int64|Optional|ResolvesToInt|int $step An integer that specifies the increment value. Can be any valid expression that resolves to a non-zero integer. Defaults to 1.
     */
    public static function range(
        Int64|ResolvesToInt|int $start,
        Int64|ResolvesToInt|int $end,
        Int64|ResolvesToInt|Optional|int $step = Optional::Undefined,
    ): RangeAggregation
    {
        return new RangeAggregation($start, $end, $step);
    }

    /**
     * Returns the document position (known as the rank) relative to other documents in the $setWindowFields stage partition.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rank/
     */
    public static function rank(): RankAggregation
    {
        return new RankAggregation();
    }

    /**
     * Applies an expression to each element in an array and combines them into a single value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/
     * @param BSONArray|PackedArray|ResolvesToArray|list $input Can be any valid expression that resolves to an array.
     * If the argument resolves to a value of null or refers to a missing field, $reduce returns null.
     * If the argument does not resolve to an array or null nor refers to a missing field, $reduce returns an error.
     * @param ExpressionInterface|mixed $initialValue The initial cumulative value set before in is applied to the first element of the input array.
     * @param ExpressionInterface|mixed $in A valid expression that $reduce applies to each element in the input array in left-to-right order. Wrap the input value with $reverseArray to yield the equivalent of applying the combining expression from right-to-left.
     * During evaluation of the in expression, two variables will be available:
     * - value is the variable that represents the cumulative value of the expression.
     * - this is the variable that refers to the element being processed.
     */
    public static function reduce(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        mixed $initialValue,
        mixed $in,
    ): ReduceAggregation
    {
        return new ReduceAggregation($input, $initialValue, $in);
    }

    /**
     * Applies a regular expression (regex) to a string and returns information on the first matched substring.
     * New in version 4.2.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFind/
     * @param ResolvesToString|non-empty-string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string.
     * @param Regex|ResolvesToString|non-empty-string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options)
     * @param Optional|non-empty-string $options
     */
    public static function regexFind(
        ResolvesToString|string $input,
        Regex|ResolvesToString|string $regex,
        Optional|string $options = Optional::Undefined,
    ): RegexFindAggregation
    {
        return new RegexFindAggregation($input, $regex, $options);
    }

    /**
     * Applies a regular expression (regex) to a string and returns information on the all matched substrings.
     * New in version 4.2.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/
     * @param ResolvesToString|non-empty-string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string.
     * @param Regex|ResolvesToString|non-empty-string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options)
     * @param Optional|non-empty-string $options
     */
    public static function regexFindAll(
        ResolvesToString|string $input,
        Regex|ResolvesToString|string $regex,
        Optional|string $options = Optional::Undefined,
    ): RegexFindAllAggregation
    {
        return new RegexFindAllAggregation($input, $regex, $options);
    }

    /**
     * Applies a regular expression (regex) to a string and returns a boolean that indicates if a match is found or not.
     * New in version 4.2.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/
     * @param ResolvesToString|non-empty-string $input The string on which you wish to apply the regex pattern. Can be a string or any valid expression that resolves to a string.
     * @param Regex|ResolvesToString|non-empty-string $regex The regex pattern to apply. Can be any valid expression that resolves to either a string or regex pattern /<pattern>/. When using the regex /<pattern>/, you can also specify the regex options i and m (but not the s or x options)
     * @param Optional|non-empty-string $options
     */
    public static function regexMatch(
        ResolvesToString|string $input,
        Regex|ResolvesToString|string $regex,
        Optional|string $options = Optional::Undefined,
    ): RegexMatchAggregation
    {
        return new RegexMatchAggregation($input, $regex, $options);
    }

    /**
     * Replaces all instances of a search string in an input string with a replacement string.
     * $replaceAll is both case-sensitive and diacritic-sensitive, and ignores any collation present on a collection.
     * New in version 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceAll/
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $input The string on which you wish to apply the find. Can be any valid expression that resolves to a string or a null. If input refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $find The string to search for within the given input. Can be any valid expression that resolves to a string or a null. If find refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $replacement The string to use to replace all matched instances of find in input. Can be any valid expression that resolves to a string or a null.
     */
    public static function replaceAll(
        ResolvesToNull|ResolvesToString|null|string $input,
        ResolvesToNull|ResolvesToString|null|string $find,
        ResolvesToNull|ResolvesToString|null|string $replacement,
    ): ReplaceAllAggregation
    {
        return new ReplaceAllAggregation($input, $find, $replacement);
    }

    /**
     * Replaces the first instance of a matched string in a given input.
     * New in version 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceOne/
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $input The string on which you wish to apply the find. Can be any valid expression that resolves to a string or a null. If input refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $find The string to search for within the given input. Can be any valid expression that resolves to a string or a null. If find refers to a field that is missing, $replaceAll returns null.
     * @param ResolvesToNull|ResolvesToString|non-empty-string|null $replacement The string to use to replace all matched instances of find in input. Can be any valid expression that resolves to a string or a null.
     */
    public static function replaceOne(
        ResolvesToNull|ResolvesToString|null|string $input,
        ResolvesToNull|ResolvesToString|null|string $find,
        ResolvesToNull|ResolvesToString|null|string $replacement,
    ): ReplaceOneAggregation
    {
        return new ReplaceOneAggregation($input, $find, $replacement);
    }

    /**
     * Returns an array with the elements in reverse order.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reverseArray/
     * @param BSONArray|PackedArray|ResolvesToArray|list $expression The argument can be any valid expression as long as it resolves to an array.
     */
    public static function reverseArray(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
    ): ReverseArrayAggregation
    {
        return new ReverseArrayAggregation($expression);
    }

    /**
     * Rounds a number to to a whole integer or to a specified decimal place.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/round/
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $round returns an error if the expression resolves to a non-numeric data type.
     * @param Int64|Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive.
     */
    public static function round(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $number,
        Int64|ResolvesToInt|Optional|int $place = Optional::Undefined,
    ): RoundAggregation
    {
        return new RoundAggregation($number, $place);
    }

    /**
     * Removes whitespace characters, including null, or the specified characters from the end of a string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rtrim/
     * @param ResolvesToString|non-empty-string $input The string to trim. The argument can be any valid expression that resolves to a string.
     * @param Optional|ResolvesToString|non-empty-string $chars The character(s) to trim from the beginning of the input.
     * The argument can be any valid expression that resolves to a string. The $ltrim operator breaks down the string into individual UTF code point to trim from input.
     * If unspecified, $ltrim removes whitespace characters, including the null character.
     */
    public static function rtrim(
        ResolvesToString|string $input,
        ResolvesToString|Optional|string $chars = Optional::Undefined,
    ): RtrimAggregation
    {
        return new RtrimAggregation($input, $chars);
    }

    /**
     * Randomly select documents at a given rate. Although the exact number of documents selected varies on each run, the quantity chosen approximates the sample rate expressed as a percentage of the total number of documents.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sampleRate/
     * @param Int64|ResolvesToFloat|float|int $rate The selection process uses a uniform random distribution. The sample rate is a floating point number between 0 and 1, inclusive, which represents the probability that a given document will be selected as it passes through the pipeline.
     * For example, a sample rate of 0.33 selects roughly one document in three.
     */
    public static function sampleRate(Int64|ResolvesToFloat|float|int $rate): SampleRateAggregation
    {
        return new SampleRateAggregation($rate);
    }

    /**
     * Returns the seconds for a date as a number between 0 and 60 (leap seconds).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/second/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function second(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): SecondAggregation
    {
        return new SecondAggregation($date, $timezone);
    }

    /**
     * Returns a set with elements that appear in the first set but not in the second set; i.e. performs a relative complement of the second set relative to the first. Accepts exactly two argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setDifference/
     * @param BSONArray|PackedArray|ResolvesToArray|list $expression1 The arguments can be any valid expression as long as they each resolve to an array.
     * @param BSONArray|PackedArray|ResolvesToArray|list $expression2 The arguments can be any valid expression as long as they each resolve to an array.
     */
    public static function setDifference(
        PackedArray|ResolvesToArray|BSONArray|array $expression1,
        PackedArray|ResolvesToArray|BSONArray|array $expression2,
    ): SetDifferenceAggregation
    {
        return new SetDifferenceAggregation($expression1, $expression2);
    }

    /**
     * Returns true if the input sets have the same distinct elements. Accepts two or more argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setEquals/
     * @param BSONArray|PackedArray|ResolvesToArray|list ...$expression
     */
    public static function setEquals(PackedArray|ResolvesToArray|BSONArray|array ...$expression): SetEqualsAggregation
    {
        return new SetEqualsAggregation(...$expression);
    }

    /**
     * Adds, updates, or removes a specified field in a document. You can use $setField to add, update, or remove fields with names that contain periods (.) or start with dollar signs ($).
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/
     * @param ResolvesToString|non-empty-string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant.
     * @param Document|ResolvesToObject|Serializable|array|stdClass $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined.
     * @param ExpressionInterface|mixed $value The value that you want to assign to field. value can be any valid expression.
     * Set to $$REMOVE to remove field from the input document.
     */
    public static function setField(
        ResolvesToString|string $field,
        Document|Serializable|ResolvesToObject|stdClass|array $input,
        mixed $value,
    ): SetFieldAggregation
    {
        return new SetFieldAggregation($field, $input, $value);
    }

    /**
     * Returns a set with elements that appear in all of the input sets. Accepts any number of argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIntersection/
     * @param BSONArray|PackedArray|ResolvesToArray|list ...$expression
     */
    public static function setIntersection(
        PackedArray|ResolvesToArray|BSONArray|array ...$expression,
    ): SetIntersectionAggregation
    {
        return new SetIntersectionAggregation(...$expression);
    }

    /**
     * Returns true if all elements of the first set appear in the second set, including when the first set equals the second set; i.e. not a strict subset. Accepts exactly two argument expressions.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIsSubset/
     * @param BSONArray|PackedArray|ResolvesToArray|list $expression1
     * @param BSONArray|PackedArray|ResolvesToArray|list $expression2
     */
    public static function setIsSubset(
        PackedArray|ResolvesToArray|BSONArray|array $expression1,
        PackedArray|ResolvesToArray|BSONArray|array $expression2,
    ): SetIsSubsetAggregation
    {
        return new SetIsSubsetAggregation($expression1, $expression2);
    }

    /**
     * Returns a set with elements that appear in any of the input sets.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setUnion/
     * @param BSONArray|PackedArray|ResolvesToArray|list ...$expression
     */
    public static function setUnion(PackedArray|ResolvesToArray|BSONArray|array ...$expression): SetUnionAggregation
    {
        return new SetUnionAggregation(...$expression);
    }

    /**
     * Returns the value from an expression applied to a document in a specified position relative to the current document in the $setWindowFields stage partition.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/
     * @param ExpressionInterface|mixed $output Specifies an expression to evaluate and return in the output.
     * @param Int64|int $by Specifies an integer with a numeric document position relative to the current document in the output.
     * For example:
     * 1 specifies the document position after the current document.
     * -1 specifies the document position before the current document.
     * -2 specifies the document position that is two positions before the current document.
     * @param ExpressionInterface|mixed $default Specifies an optional default expression to evaluate if the document position is outside of the implicit $setWindowFields stage window. The implicit window contains all the documents in the partition.
     * The default expression must evaluate to a constant value.
     * If you do not specify a default expression, $shift returns null for documents whose positions are outside of the implicit $setWindowFields stage window.
     */
    public static function shift(mixed $output, Int64|int $by, mixed $default): ShiftAggregation
    {
        return new ShiftAggregation($output, $by, $default);
    }

    /**
     * Returns the sine of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sin/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $sin takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $sin returns values as a double. $sin can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function sin(Decimal128|Int64|ResolvesToNumber|float|int $expression): SinAggregation
    {
        return new SinAggregation($expression);
    }

    /**
     * Returns the hyperbolic sine of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sinh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $sinh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $sinh returns values as a double. $sinh can also return values as a 128-bit decimal if the expression resolves to a 128-bit decimal value.
     */
    public static function sinh(Decimal128|Int64|ResolvesToNumber|float|int $expression): SinhAggregation
    {
        return new SinhAggregation($expression);
    }

    /**
     * Returns the number of elements in the array. Accepts a single expression as argument.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/size/
     * @param BSONArray|PackedArray|ResolvesToArray|list $expression The argument for $size can be any expression as long as it resolves to an array.
     */
    public static function size(PackedArray|ResolvesToArray|BSONArray|array $expression): SizeAggregation
    {
        return new SizeAggregation($expression);
    }

    /**
     * Returns a subset of an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/slice/
     * @param BSONArray|PackedArray|ResolvesToArray|list $expression Any valid expression as long as it resolves to an array.
     * @param Int64|ResolvesToInt|int $n Any valid expression as long as it resolves to an integer. If position is specified, n must resolve to a positive integer.
     * If positive, $slice returns up to the first n elements in the array. If the position is specified, $slice returns the first n elements starting from the position.
     * If negative, $slice returns up to the last n elements in the array. n cannot resolve to a negative number if <position> is specified.
     * @param Int64|Optional|ResolvesToInt|int $position Any valid expression as long as it resolves to an integer.
     * If positive, $slice determines the starting position from the start of the array. If position is greater than the number of elements, the $slice returns an empty array.
     * If negative, $slice determines the starting position from the end of the array. If the absolute value of the <position> is greater than the number of elements, the starting position is the start of the array.
     */
    public static function slice(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
        Int64|ResolvesToInt|int $n,
        Int64|ResolvesToInt|Optional|int $position = Optional::Undefined,
    ): SliceAggregation
    {
        return new SliceAggregation($expression, $n, $position);
    }

    /**
     * Sorts the elements of an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/
     * @param BSONArray|PackedArray|ResolvesToArray|list $input The array to be sorted.
     * @param array|stdClass $sortBy The document specifies a sort ordering.
     */
    public static function sortArray(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        stdClass|array $sortBy,
    ): SortArrayAggregation
    {
        return new SortArrayAggregation($input, $sortBy);
    }

    /**
     * Splits a string into substrings based on a delimiter. Returns an array of substrings. If the delimiter is not found within the string, returns an array containing the original string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/split/
     * @param ResolvesToString|non-empty-string $string The string to be split. string expression can be any valid expression as long as it resolves to a string.
     * @param ResolvesToString|non-empty-string $delimiter The delimiter to use when splitting the string expression. delimiter can be any valid expression as long as it resolves to a string.
     */
    public static function split(
        ResolvesToString|string $string,
        ResolvesToString|string $delimiter,
    ): SplitAggregation
    {
        return new SplitAggregation($string, $delimiter);
    }

    /**
     * Calculates the square root.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sqrt/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number The argument can be any valid expression as long as it resolves to a non-negative number.
     */
    public static function sqrt(Decimal128|Int64|ResolvesToNumber|float|int $number): SqrtAggregation
    {
        return new SqrtAggregation($number);
    }

    /**
     * Calculates the population standard deviation of the input values. Use if the values encompass the entire population of data you want to represent and do not wish to generalize about a larger population. $stdDevPop ignores non-numeric values.
     * If the values represent only a sample of a population of data from which to generalize about the population, use $stdDevSamp instead.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function stdDevPop(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): StdDevPopAggregation
    {
        return new StdDevPopAggregation(...$expression);
    }

    /**
     * Calculates the sample standard deviation of the input values. Use if the values encompass a sample of a population of data from which to generalize about the population. $stdDevSamp ignores non-numeric values.
     * If the values represent the entire population of data or you do not wish to generalize about a larger population, use $stdDevPop instead.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function stdDevSamp(
        Decimal128|Int64|ResolvesToNumber|float|int ...$expression,
    ): StdDevSampAggregation
    {
        return new StdDevSampAggregation(...$expression);
    }

    /**
     * Performs case-insensitive string comparison and returns: 0 if two strings are equivalent, 1 if the first string is greater than the second, and -1 if the first string is less than the second.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strcasecmp/
     * @param ResolvesToString|non-empty-string $expression1
     * @param ResolvesToString|non-empty-string $expression2
     */
    public static function strcasecmp(
        ResolvesToString|string $expression1,
        ResolvesToString|string $expression2,
    ): StrcasecmpAggregation
    {
        return new StrcasecmpAggregation($expression1, $expression2);
    }

    /**
     * Returns the number of UTF-8 encoded bytes in a string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenBytes/
     * @param ResolvesToString|non-empty-string $expression
     */
    public static function strLenBytes(ResolvesToString|string $expression): StrLenBytesAggregation
    {
        return new StrLenBytesAggregation($expression);
    }

    /**
     * Returns the number of UTF-8 code points in a string.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenCP/
     * @param ResolvesToString|non-empty-string $expression
     */
    public static function strLenCP(ResolvesToString|string $expression): StrLenCPAggregation
    {
        return new StrLenCPAggregation($expression);
    }

    /**
     * Deprecated. Use $substrBytes or $substrCP.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substr/
     * @param ResolvesToString|non-empty-string $string
     * @param Int64|ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "".
     * @param Int64|ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public static function substr(
        ResolvesToString|string $string,
        Int64|ResolvesToInt|int $start,
        Int64|ResolvesToInt|int $length,
    ): SubstrAggregation
    {
        return new SubstrAggregation($string, $start, $length);
    }

    /**
     * Returns the substring of a string. Starts with the character at the specified UTF-8 byte index (zero-based) in the string and continues for the specified number of bytes.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrBytes/
     * @param ResolvesToString|non-empty-string $string
     * @param Int64|ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "".
     * @param Int64|ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public static function substrBytes(
        ResolvesToString|string $string,
        Int64|ResolvesToInt|int $start,
        Int64|ResolvesToInt|int $length,
    ): SubstrBytesAggregation
    {
        return new SubstrBytesAggregation($string, $start, $length);
    }

    /**
     * Returns the substring of a string. Starts with the character at the specified UTF-8 code point (CP) index (zero-based) in the string and continues for the number of code points specified.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrCP/
     * @param ResolvesToString|non-empty-string $string
     * @param Int64|ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "".
     * @param Int64|ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public static function substrCP(
        ResolvesToString|string $string,
        Int64|ResolvesToInt|int $start,
        Int64|ResolvesToInt|int $length,
    ): SubstrCPAggregation
    {
        return new SubstrCPAggregation($string, $start, $length);
    }

    /**
     * Returns the result of subtracting the second value from the first. If the two values are numbers, return the difference. If the two values are dates, return the difference in milliseconds. If the two values are a date and a number in milliseconds, return the resulting date. Accepts two argument expressions. If the two values are a date and a number, specify the date argument first as it is not meaningful to subtract a date from a number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression1
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression2
     */
    public static function subtract(
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $expression1,
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $expression2,
    ): SubtractAggregation
    {
        return new SubtractAggregation($expression1, $expression2);
    }

    /**
     * Returns a sum of numerical values. Ignores non-numeric values.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function sum(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): SumAggregation
    {
        return new SumAggregation(...$expression);
    }

    /**
     * Evaluates a series of case expressions. When it finds an expression which evaluates to true, $switch executes a specified expression and breaks out of the control flow.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/
     * @param BSONArray|PackedArray|list $branches An array of control branch documents. Each branch is a document with the following fields:
     * - case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * - then Can be any valid expression.
     * The branches array must contain at least one branch document.
     * @param ExpressionInterface|Optional|mixed $default The path to take if no branch case expression evaluates to true.
     * Although optional, if default is unspecified and no branch case evaluates to true, $switch returns an error.
     */
    public static function switch(
        PackedArray|BSONArray|array $branches,
        mixed $default = Optional::Undefined,
    ): SwitchAggregation
    {
        return new SwitchAggregation($branches, $default);
    }

    /**
     * Returns the tangent of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tan/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $tan takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $tan returns values as a double. $tan can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function tan(Decimal128|Int64|ResolvesToNumber|float|int $expression): TanAggregation
    {
        return new TanAggregation($expression);
    }

    /**
     * Returns the hyperbolic tangent of a value that is measured in radians.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tanh/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $tanh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $tanh returns values as a double. $tanh can also return values as a 128-bit decimal if the expression resolves to a 128-bit decimal value.
     */
    public static function tanh(Decimal128|Int64|ResolvesToNumber|float|int $expression): TanhAggregation
    {
        return new TanhAggregation($expression);
    }

    /**
     * Converts value to a boolean.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toBool/
     * @param ExpressionInterface|mixed $expression
     */
    public static function toBool(mixed $expression): ToBoolAggregation
    {
        return new ToBoolAggregation($expression);
    }

    /**
     * Converts value to a Date.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDate/
     * @param ExpressionInterface|mixed $expression
     */
    public static function toDate(mixed $expression): ToDateAggregation
    {
        return new ToDateAggregation($expression);
    }

    /**
     * Converts value to a Decimal128.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDecimal/
     * @param ExpressionInterface|mixed $expression
     */
    public static function toDecimal(mixed $expression): ToDecimalAggregation
    {
        return new ToDecimalAggregation($expression);
    }

    /**
     * Converts value to a double.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDouble/
     * @param ExpressionInterface|mixed $expression
     */
    public static function toDouble(mixed $expression): ToDoubleAggregation
    {
        return new ToDoubleAggregation($expression);
    }

    /**
     * Converts value to an integer.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toInt/
     * @param ExpressionInterface|mixed $expression
     */
    public static function toInt(mixed $expression): ToIntAggregation
    {
        return new ToIntAggregation($expression);
    }

    /**
     * Converts value to a long.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLong/
     * @param ExpressionInterface|mixed $expression
     */
    public static function toLong(mixed $expression): ToLongAggregation
    {
        return new ToLongAggregation($expression);
    }

    /**
     * Converts a string to lowercase. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLower/
     * @param ResolvesToString|non-empty-string $expression
     */
    public static function toLower(ResolvesToString|string $expression): ToLowerAggregation
    {
        return new ToLowerAggregation($expression);
    }

    /**
     * Converts value to an ObjectId.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toObjectId/
     * @param ExpressionInterface|mixed $expression
     */
    public static function toObjectId(mixed $expression): ToObjectIdAggregation
    {
        return new ToObjectIdAggregation($expression);
    }

    /**
     * Returns the top element within a group according to the specified sort order.
     * New in version 5.2.
     *
     * Available in the $group and $setWindowFields stages.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/top/
     * @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public static function top(stdClass|array $sortBy, mixed $output): TopAggregation
    {
        return new TopAggregation($sortBy, $output);
    }

    /**
     * Returns an aggregation of the top n fields within a group, according to the specified sort order.
     * New in version 5.2.
     *
     * Available in the $group and $setWindowFields stages.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/
     * @param Int64|ResolvesToInt|int $n limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group.
     * @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public static function topN(Int64|ResolvesToInt|int $n, stdClass|array $sortBy, mixed $output): TopNAggregation
    {
        return new TopNAggregation($n, $sortBy, $output);
    }

    /**
     * Converts value to a string.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toString/
     * @param ExpressionInterface|mixed $expression
     */
    public static function toString(mixed $expression): ToStringAggregation
    {
        return new ToStringAggregation($expression);
    }

    /**
     * Converts a string to uppercase. Accepts a single argument expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toUpper/
     * @param ResolvesToString|non-empty-string $expression
     */
    public static function toUpper(ResolvesToString|string $expression): ToUpperAggregation
    {
        return new ToUpperAggregation($expression);
    }

    /**
     * Removes whitespace or the specified characters from the beginning and end of a string.
     * New in version 4.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trim/
     * @param ResolvesToString|non-empty-string $input The string to trim. The argument can be any valid expression that resolves to a string.
     * @param Optional|ResolvesToString|non-empty-string $chars The character(s) to trim from the beginning of the input.
     * The argument can be any valid expression that resolves to a string. The $ltrim operator breaks down the string into individual UTF code point to trim from input.
     * If unspecified, $ltrim removes whitespace characters, including the null character.
     */
    public static function trim(
        ResolvesToString|string $input,
        ResolvesToString|Optional|string $chars = Optional::Undefined,
    ): TrimAggregation
    {
        return new TrimAggregation($input, $chars);
    }

    /**
     * Truncates a number to a whole integer or to a specified decimal place.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trunc/
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $trunc returns an error if the expression resolves to a non-numeric data type.
     * @param Int64|Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. e.g. -20 < place < 100. Defaults to 0.
     */
    public static function trunc(
        Decimal128|Int64|ResolvesToNumber|float|int $number,
        Int64|ResolvesToInt|Optional|int $place = Optional::Undefined,
    ): TruncAggregation
    {
        return new TruncAggregation($number, $place);
    }

    /**
     * Returns the incrementing ordinal from a timestamp as a long.
     * New in version 5.1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsIncrement/
     * @param Int64|ResolvesToTimestamp|int $expression
     */
    public static function tsIncrement(Int64|ResolvesToTimestamp|int $expression): TsIncrementAggregation
    {
        return new TsIncrementAggregation($expression);
    }

    /**
     * Returns the seconds from a timestamp as a long.
     * New in version 5.1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/
     * @param Int64|ResolvesToTimestamp|int $expression
     */
    public static function tsSecond(Int64|ResolvesToTimestamp|int $expression): TsSecondAggregation
    {
        return new TsSecondAggregation($expression);
    }

    /**
     * Return the BSON data type of the field.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/type/
     * @param ExpressionInterface|mixed $expression
     */
    public static function type(mixed $expression): TypeAggregation
    {
        return new TypeAggregation($expression);
    }

    /**
     * You can use $unsetField to remove fields with names that contain periods (.) or that start with dollar signs ($).
     * $unsetField is an alias for $setField using $$REMOVE to remove fields.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/
     * @param ResolvesToString|non-empty-string $field Field in the input object that you want to add, update, or remove. field can be any valid expression that resolves to a string constant.
     * @param Document|ResolvesToObject|Serializable|array|stdClass $input Document that contains the field that you want to add or update. input must resolve to an object, missing, null, or undefined.
     */
    public static function unsetField(
        ResolvesToString|string $field,
        Document|Serializable|ResolvesToObject|stdClass|array $input,
    ): UnsetFieldAggregation
    {
        return new UnsetFieldAggregation($field, $input);
    }

    /**
     * Returns the week number for a date as a number between 0 (the partial week that precedes the first Sunday of the year) and 53 (leap year).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/week/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function week(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): WeekAggregation
    {
        return new WeekAggregation($date, $timezone);
    }

    /**
     * Returns the year for a date as a number (e.g. 2014).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/year/
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function year(
        DateTimeInterface|Int64|ObjectId|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): YearAggregation
    {
        return new YearAggregation($date, $timezone);
    }

    /**
     * Merge two arrays together.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/zip/
     * @param BSONArray|PackedArray|ResolvesToArray|list $inputs An array of expressions that resolve to arrays. The elements of these input arrays combine to form the arrays of the output array.
     * If any of the inputs arrays resolves to a value of null or refers to a missing field, $zip returns null.
     * If any of the inputs arrays does not resolve to an array or null nor refers to a missing field, $zip returns an error.
     * @param bool $useLongestLength A boolean which specifies whether the length of the longest array determines the number of arrays in the output array.
     * The default value is false: the shortest array length determines the number of arrays in the output array.
     * @param BSONArray|PackedArray|list $defaults An array of default element values to use if the input arrays have different lengths. You must specify useLongestLength: true along with this field, or else $zip will return an error.
     * If useLongestLength: true but defaults is empty or not specified, $zip uses null as the default value.
     * If specifying a non-empty defaults, you must specify a default for each input array or else $zip will return an error.
     */
    public static function zip(
        PackedArray|ResolvesToArray|BSONArray|array $inputs,
        bool $useLongestLength,
        PackedArray|BSONArray|array $defaults,
    ): ZipAggregation
    {
        return new ZipAggregation($inputs, $useLongestLength, $defaults);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
