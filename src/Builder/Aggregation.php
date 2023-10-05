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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $value
     */
    public static function abs(Decimal128|Int64|ResolvesToNumber|float|int $value): AbsAggregation
    {
        return new AbsAggregation($value);
    }

    /**
     * @param non-empty-string $init Function used to initialize the state. The init function receives its arguments from the initArgs array expression. You can specify the function definition as either BSON type Code or String.
     * @param non-empty-string $accumulate Function used to accumulate documents. The accumulate function receives its arguments from the current state and accumulateArgs array expression. The result of the accumulate function becomes the new state. You can specify the function definition as either BSON type Code or String.
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $accumulateArgs Arguments passed to the accumulate function. You can use accumulateArgs to specify what field value(s) to pass to the accumulate function.
     * @param non-empty-string $merge Function used to merge two internal states. merge must be either a String or Code BSON type. merge returns the combined result of the two merged states. For information on when the merge function is called, see Merge Two States with $merge.
     * @param non-empty-string $lang The language used in the $accumulator code.
     * @param BSONArray|Optional|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $initArgs Arguments passed to the init function.
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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $acos takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $acos returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acos returns values as a double. $acos can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function acos(Decimal128|Int64|ResolvesToNumber|float|int $expression): AcosAggregation
    {
        return new AcosAggregation($expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $acosh takes any valid expression that resolves to a number between 1 and +Infinity, e.g. 1 <= value <= +Infinity.
     * $acosh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acosh returns values as a double. $acosh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function acosh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AcoshAggregation
    {
        return new AcoshAggregation($expression);
    }

    /**
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int ...$expression The arguments can be any valid expression as long as they resolve to either all numbers or to numbers and a date.
     */
    public static function add(
        \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int ...$expression,
    ): AddAggregation
    {
        return new AddAggregation(...$expression);
    }

    /**
     * @param ExpressionInterface|FieldPath|mixed|non-empty-string $expression
     */
    public static function addToSet(mixed $expression): AddToSetAggregation
    {
        return new AddToSetAggregation($expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> ...$expression
     */
    public static function allElementsTrue(
        PackedArray|ResolvesToArray|BSONArray|array ...$expression,
    ): AllElementsTrueAggregation
    {
        return new AllElementsTrueAggregation(...$expression);
    }

    /**
     * @param Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|bool|float|int|mixed|non-empty-string|null ...$expression
     */
    public static function and(mixed ...$expression): AndAggregation
    {
        return new AndAggregation(...$expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression
     */
    public static function anyElementTrue(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
    ): AnyElementTrueAggregation
    {
        return new AnyElementTrueAggregation($expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $array
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
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $array
     */
    public static function arrayToObject(PackedArray|ResolvesToArray|BSONArray|array $array): ArrayToObjectAggregation
    {
        return new ArrayToObjectAggregation($array);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $asin takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $asin returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asin returns values as a double. $asin can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function asin(Decimal128|Int64|ResolvesToNumber|float|int $expression): AsinAggregation
    {
        return new AsinAggregation($expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function asinh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AsinhAggregation
    {
        return new AsinhAggregation($expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $atan takes any valid expression that resolves to a number.
     * $atan returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atan returns values as a double. $atan can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function atan(Decimal128|Int64|ResolvesToNumber|float|int $expression): AtanAggregation
    {
        return new AtanAggregation($expression);
    }

    /**
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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $atanh takes any valid expression that resolves to a number between -1 and 1, e.g. -1 <= value <= 1.
     * $atanh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atanh returns values as a double. $atanh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function atanh(Decimal128|Int64|ResolvesToNumber|float|int $expression): AtanhAggregation
    {
        return new AtanhAggregation($expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function avg(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): AvgAggregation
    {
        return new AvgAggregation(...$expression);
    }

    /**
     * @param Binary|ResolvesToBinary|ResolvesToNull|ResolvesToString|non-empty-string|null $expression
     */
    public static function binarySize(
        Binary|ResolvesToBinary|ResolvesToNull|ResolvesToString|null|string $expression,
    ): BinarySizeAggregation
    {
        return new BinarySizeAggregation($expression);
    }

    /**
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public static function bitAnd(Int64|ResolvesToInt|ResolvesToLong|int ...$expression): BitAndAggregation
    {
        return new BitAndAggregation(...$expression);
    }

    /**
     * @param Int64|ResolvesToInt|ResolvesToLong|int $expression
     */
    public static function bitNot(Int64|ResolvesToInt|ResolvesToLong|int $expression): BitNotAggregation
    {
        return new BitNotAggregation($expression);
    }

    /**
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public static function bitOr(Int64|ResolvesToInt|ResolvesToLong|int ...$expression): BitOrAggregation
    {
        return new BitOrAggregation(...$expression);
    }

    public static function bitXor(): BitXorAggregation
    {
        return new BitXorAggregation();
    }

    /**
     * @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public static function bottom(stdClass|array $sortBy, mixed $output): BottomAggregation
    {
        return new BottomAggregation($sortBy, $output);
    }

    /**
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
     * @param Document|ResolvesToNull|ResolvesToObject|Serializable|array|null|stdClass $object
     */
    public static function bsonSize(
        Document|Serializable|ResolvesToNull|ResolvesToObject|stdClass|array|null $object,
    ): BsonSizeAggregation
    {
        return new BsonSizeAggregation($object);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression If the argument resolves to a value of null or refers to a field that is missing, $ceil returns null. If the argument resolves to NaN, $ceil returns NaN.
     */
    public static function ceil(Decimal128|Int64|ResolvesToNumber|float|int $expression): CeilAggregation
    {
        return new CeilAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function cmp(mixed $expression1, mixed $expression2): CmpAggregation
    {
        return new CmpAggregation($expression1, $expression2);
    }

    /**
     * @param ResolvesToString|non-empty-string ...$expression
     */
    public static function concat(ResolvesToString|string ...$expression): ConcatAggregation
    {
        return new ConcatAggregation(...$expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> ...$array
     */
    public static function concatArrays(
        PackedArray|ResolvesToArray|BSONArray|array ...$array,
    ): ConcatArraysAggregation
    {
        return new ConcatArraysAggregation(...$array);
    }

    /**
     * @param ResolvesToBool|bool $if
     * @param ExpressionInterface|mixed $then
     * @param ExpressionInterface|mixed $else
     */
    public static function cond(ResolvesToBool|bool $if, mixed $then, mixed $else): CondAggregation
    {
        return new CondAggregation($if, $then, $else);
    }

    /**
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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $cos takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $cos returns values as a double. $cos can also return values as a 128-bit decimal as long as the <expression> resolves to a 128-bit decimal value.
     */
    public static function cos(Decimal128|Int64|ResolvesToNumber|float|int $expression): CosAggregation
    {
        return new CosAggregation($expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $cosh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $cosh returns values as a double. $cosh can also return values as a 128-bit decimal if the <expression> resolves to a 128-bit decimal value.
     */
    public static function cosh(Decimal128|Int64|ResolvesToNumber|float|int $expression): CoshAggregation
    {
        return new CoshAggregation($expression);
    }

    /**
     * @param non-empty-string $field
     */
    public static function count(string $field): CountAggregation
    {
        return new CountAggregation($field);
    }

    /**
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
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The unit used to measure the amount of time added to the startDate.
     * @param Int64|ResolvesToInt|ResolvesToLong|int $amount
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dateAdd(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ResolvesToString|string $unit,
        Int64|ResolvesToInt|ResolvesToLong|int $amount,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DateAddAggregation
    {
        return new DateAddAggregation($startDate, $unit, $amount, $timezone);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $startDate The start of the time period. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $endDate The end of the time period. The endDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The time measurement unit between the startDate and endDate
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|ResolvesToString|non-empty-string $startOfWeek Used when the unit is equal to week. Defaults to Sunday. The startOfWeek parameter is an expression that resolves to a case insensitive string
     */
    public static function dateDiff(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $endDate,
        ResolvesToString|string $unit,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        ResolvesToString|Optional|string $startOfWeek = Optional::Undefined,
    ): DateDiffAggregation
    {
        return new DateDiffAggregation($startDate, $endDate, $unit, $timezone, $startOfWeek);
    }

    /**
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
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The unit used to measure the amount of time added to the startDate.
     * @param Int64|ResolvesToInt|ResolvesToLong|int $amount
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dateSubtract(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ResolvesToString|string $unit,
        Int64|ResolvesToInt|ResolvesToLong|int $amount,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DateSubtractAggregation
    {
        return new DateSubtractAggregation($startDate, $unit, $amount, $timezone);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The input date for which to return parts. date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|bool $iso8601 If set to true, modifies the output document to use ISO week date fields. Defaults to false.
     */
    public static function dateToParts(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        Optional|bool $iso8601 = Optional::Undefined,
    ): DateToPartsAggregation
    {
        return new DateToPartsAggregation($date, $timezone, $iso8601);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to convert to string. Must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     * @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date.
     * @param ExpressionInterface|Optional|mixed $onNull The value to return if the date is null or missing.
     * If unspecified, $dateToString returns null if the date is null or missing.
     */
    public static function dateToString(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $format = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        mixed $onNull = Optional::Undefined,
    ): DateToStringAggregation
    {
        return new DateToStringAggregation($date, $format, $timezone, $onNull);
    }

    /**
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
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|string $unit,
        Decimal128|Int64|ResolvesToNumber|Optional|float|int $binSize = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        Optional|string $startOfWeek = Optional::Undefined,
    ): DateTruncAggregation
    {
        return new DateTruncAggregation($date, $unit, $binSize, $timezone, $startOfWeek);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfMonth(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DayOfMonthAggregation
    {
        return new DayOfMonthAggregation($date, $timezone);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfWeek(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DayOfWeekAggregation
    {
        return new DayOfWeekAggregation($date, $timezone);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function dayOfYear(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): DayOfYearAggregation
    {
        return new DayOfYearAggregation($date, $timezone);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $degreesToRadians takes any valid expression that resolves to a number.
     * By default $degreesToRadians returns values as a double. $degreesToRadians can also return values as a 128-bit decimal as long as the <expression> resolves to a 128-bit decimal value.
     */
    public static function degreesToRadians(
        Decimal128|Int64|ResolvesToNumber|float|int $expression,
    ): DegreesToRadiansAggregation
    {
        return new DegreesToRadiansAggregation($expression);
    }

    public static function denseRank(): DenseRankAggregation
    {
        return new DenseRankAggregation();
    }

    /**
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public static function derivative(
        \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int $input,
        Optional|string $unit = Optional::Undefined,
    ): DerivativeAggregation
    {
        return new DerivativeAggregation($input, $unit);
    }

    /**
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

    public static function documentNumber(): DocumentNumberAggregation
    {
        return new DocumentNumberAggregation();
    }

    /**
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function eq(mixed $expression1, mixed $expression2): EqAggregation
    {
        return new EqAggregation($expression1, $expression2);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $exponent
     */
    public static function exp(Decimal128|Int64|ResolvesToNumber|float|int $exponent): ExpAggregation
    {
        return new ExpAggregation($exponent);
    }

    /**
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
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input
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
     * @param ExpressionInterface|mixed $expression
     */
    public static function first(mixed $expression): FirstAggregation
    {
        return new FirstAggregation($expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input An expression that resolves to the array from which to return n elements.
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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public static function floor(Decimal128|Int64|ResolvesToNumber|float|int $expression): FloorAggregation
    {
        return new FloorAggregation($expression);
    }

    /**
     * @param non-empty-string $body The function definition. You can specify the function definition as either BSON type Code or String.
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $args Arguments passed to the function body. If the body function does not take an argument, you can specify an empty array [ ].
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
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function gt(mixed $expression1, mixed $expression2): GtAggregation
    {
        return new GtAggregation($expression1, $expression2);
    }

    /**
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function gte(mixed $expression1, mixed $expression2): GteAggregation
    {
        return new GteAggregation($expression1, $expression2);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function hour(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): HourAggregation
    {
        return new HourAggregation($date, $timezone);
    }

    /**
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function ifNull(mixed ...$expression): IfNullAggregation
    {
        return new IfNullAggregation(...$expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression Any valid expression expression.
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $array Any valid expression that resolves to an array.
     */
    public static function in(mixed $expression, PackedArray|ResolvesToArray|BSONArray|array $array): InAggregation
    {
        return new InAggregation($expression, $array);
    }

    /**
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
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|ResolvesToString|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public static function integral(
        \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int $input,
        ResolvesToString|Optional|string $unit = Optional::Undefined,
    ): IntegralAggregation
    {
        return new IntegralAggregation($input, $unit);
    }

    /**
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function isArray(mixed ...$expression): IsArrayAggregation
    {
        return new IsArrayAggregation(...$expression);
    }

    /**
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function isNumber(mixed ...$expression): IsNumberAggregation
    {
        return new IsNumberAggregation(...$expression);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoDayOfWeek(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): IsoDayOfWeekAggregation
    {
        return new IsoDayOfWeekAggregation($date, $timezone);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoWeek(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): IsoWeekAggregation
    {
        return new IsoWeekAggregation($date, $timezone);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function isoWeekYear(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): IsoWeekYearAggregation
    {
        return new IsoWeekYearAggregation($date, $timezone);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function last(mixed $expression): LastAggregation
    {
        return new LastAggregation($expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input An expression that resolves to the array from which to return n elements.
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
     * @param Document|Serializable|array|stdClass $vars Assignment block for the variables accessible in the in expression. To assign a variable, specify a string for the variable name and assign a valid expression for the value.
     * The variable assignments have no meaning outside the in expression, not even within the vars block itself.
     * @param ExpressionInterface|mixed $in The expression to evaluate.
     */
    public static function let(Document|Serializable|stdClass|array $vars, mixed $in): LetAggregation
    {
        return new LetAggregation($vars, $in);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public static function linearFill(Decimal128|Int64|ResolvesToNumber|float|int $expression): LinearFillAggregation
    {
        return new LinearFillAggregation($expression);
    }

    /**
     * @param mixed $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression.
     */
    public static function literal(mixed $value): LiteralAggregation
    {
        return new LiteralAggregation($value);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number. For more information on expressions, see Expressions.
     */
    public static function ln(Decimal128|Int64|ResolvesToNumber|float|int $number): LnAggregation
    {
        return new LnAggregation($number);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function locf(mixed $expression): LocfAggregation
    {
        return new LocfAggregation($expression);
    }

    /**
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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number.
     */
    public static function log10(Decimal128|Int64|ResolvesToNumber|float|int $number): Log10Aggregation
    {
        return new Log10Aggregation($number);
    }

    /**
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function lt(mixed $expression1, mixed $expression2): LtAggregation
    {
        return new LtAggregation($expression1, $expression2);
    }

    /**
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function lte(mixed $expression1, mixed $expression2): LteAggregation
    {
        return new LteAggregation($expression1, $expression2);
    }

    /**
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
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input An expression that resolves to an array.
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
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function max(mixed ...$expression): MaxAggregation
    {
        return new MaxAggregation(...$expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input An expression that resolves to the array from which to return the maximal n elements.
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
     * @param Document|ResolvesToObject|Serializable|array|stdClass ...$document Any valid expression that resolves to a document.
     */
    public static function mergeObjects(
        Document|Serializable|ResolvesToObject|stdClass|array ...$document,
    ): MergeObjectsAggregation
    {
        return new MergeObjectsAggregation(...$document);
    }

    /**
     * @param non-empty-string $keyword
     */
    public static function meta(string $keyword): MetaAggregation
    {
        return new MetaAggregation($keyword);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function millisecond(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): MillisecondAggregation
    {
        return new MillisecondAggregation($date, $timezone);
    }

    /**
     * @param ExpressionInterface|mixed ...$expression
     */
    public static function min(mixed ...$expression): MinAggregation
    {
        return new MinAggregation(...$expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input An expression that resolves to the array from which to return the maximal n elements.
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
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function minute(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): MinuteAggregation
    {
        return new MinuteAggregation($date, $timezone);
    }

    /**
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
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function month(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): MonthAggregation
    {
        return new MonthAggregation($date, $timezone);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression The arguments can be any valid expression as long as they resolve to numbers.
     * Starting in MongoDB 6.1 you can optimize the $multiply operation. To improve performance, group references at the end of the argument list.
     */
    public static function multiply(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): MultiplyAggregation
    {
        return new MultiplyAggregation(...$expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public static function ne(mixed $expression1, mixed $expression2): NeAggregation
    {
        return new NeAggregation($expression1, $expression2);
    }

    /**
     * @param ExpressionInterface|ResolvesToBool|bool|mixed $expression
     */
    public static function not(mixed $expression): NotAggregation
    {
        return new NotAggregation($expression);
    }

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass $object Any valid expression as long as it resolves to a document object. $objectToArray applies to the top-level fields of its argument. If the argument is a document that itself contains embedded document fields, the $objectToArray does not recursively apply to the embedded document fields.
     */
    public static function objectToArray(
        Document|Serializable|ResolvesToObject|stdClass|array $object,
    ): ObjectToArrayAggregation
    {
        return new ObjectToArrayAggregation($object);
    }

    /**
     * @param ExpressionInterface|ResolvesToBool|bool|mixed ...$expression
     */
    public static function or(mixed ...$expression): OrAggregation
    {
        return new OrAggregation(...$expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $input $percentile calculates the percentile values of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $percentile calculation ignores it.
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $p $percentile calculates a percentile value for each element in p. The elements represent percentages and must evaluate to numeric values in the range 0.0 to 1.0, inclusive.
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
     * @param ExpressionInterface|mixed $expression
     */
    public static function push(mixed $expression): PushAggregation
    {
        return new PushAggregation($expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public static function radiansToDegrees(
        Decimal128|Int64|ResolvesToNumber|float|int $expression,
    ): RadiansToDegreesAggregation
    {
        return new RadiansToDegreesAggregation($expression);
    }

    public static function rand(): RandAggregation
    {
        return new RandAggregation();
    }

    /**
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

    public static function rank(): RankAggregation
    {
        return new RankAggregation();
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input Can be any valid expression that resolves to an array.
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
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression The argument can be any valid expression as long as it resolves to an array.
     */
    public static function reverseArray(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
    ): ReverseArrayAggregation
    {
        return new ReverseArrayAggregation($expression);
    }

    /**
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
     * @param Int64|ResolvesToFloat|float|int $rate The selection process uses a uniform random distribution. The sample rate is a floating point number between 0 and 1, inclusive, which represents the probability that a given document will be selected as it passes through the pipeline.
     * For example, a sample rate of 0.33 selects roughly one document in three.
     */
    public static function sampleRate(Int64|ResolvesToFloat|float|int $rate): SampleRateAggregation
    {
        return new SampleRateAggregation($rate);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function second(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): SecondAggregation
    {
        return new SecondAggregation($date, $timezone);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression1 The arguments can be any valid expression as long as they each resolve to an array.
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression2 The arguments can be any valid expression as long as they each resolve to an array.
     */
    public static function setDifference(
        PackedArray|ResolvesToArray|BSONArray|array $expression1,
        PackedArray|ResolvesToArray|BSONArray|array $expression2,
    ): SetDifferenceAggregation
    {
        return new SetDifferenceAggregation($expression1, $expression2);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> ...$expression
     */
    public static function setEquals(PackedArray|ResolvesToArray|BSONArray|array ...$expression): SetEqualsAggregation
    {
        return new SetEqualsAggregation(...$expression);
    }

    /**
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
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> ...$expression
     */
    public static function setIntersection(
        PackedArray|ResolvesToArray|BSONArray|array ...$expression,
    ): SetIntersectionAggregation
    {
        return new SetIntersectionAggregation(...$expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression1
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression2
     */
    public static function setIsSubset(
        PackedArray|ResolvesToArray|BSONArray|array $expression1,
        PackedArray|ResolvesToArray|BSONArray|array $expression2,
    ): SetIsSubsetAggregation
    {
        return new SetIsSubsetAggregation($expression1, $expression2);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> ...$expression
     */
    public static function setUnion(PackedArray|ResolvesToArray|BSONArray|array ...$expression): SetUnionAggregation
    {
        return new SetUnionAggregation(...$expression);
    }

    /**
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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $sin takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $sin returns values as a double. $sin can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function sin(Decimal128|Int64|ResolvesToNumber|float|int $expression): SinAggregation
    {
        return new SinAggregation($expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $sinh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $sinh returns values as a double. $sinh can also return values as a 128-bit decimal if the expression resolves to a 128-bit decimal value.
     */
    public static function sinh(Decimal128|Int64|ResolvesToNumber|float|int $expression): SinhAggregation
    {
        return new SinhAggregation($expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression The argument for $size can be any expression as long as it resolves to an array.
     */
    public static function size(PackedArray|ResolvesToArray|BSONArray|array $expression): SizeAggregation
    {
        return new SizeAggregation($expression);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression Any valid expression as long as it resolves to an array.
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
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input The array to be sorted.
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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number The argument can be any valid expression as long as it resolves to a non-negative number.
     */
    public static function sqrt(Decimal128|Int64|ResolvesToNumber|float|int $number): SqrtAggregation
    {
        return new SqrtAggregation($number);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function stdDevPop(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): StdDevPopAggregation
    {
        return new StdDevPopAggregation(...$expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function stdDevSamp(
        Decimal128|Int64|ResolvesToNumber|float|int ...$expression,
    ): StdDevSampAggregation
    {
        return new StdDevSampAggregation(...$expression);
    }

    /**
     * @param ResolvesToString|non-empty-string $expression
     */
    public static function strLenBytes(ResolvesToString|string $expression): StrLenBytesAggregation
    {
        return new StrLenBytesAggregation($expression);
    }

    /**
     * @param ResolvesToString|non-empty-string $expression
     */
    public static function strLenCP(ResolvesToString|string $expression): StrLenCPAggregation
    {
        return new StrLenCPAggregation($expression);
    }

    /**
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
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression1
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression2
     */
    public static function subtract(
        \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int $expression1,
        \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int $expression2,
    ): SubtractAggregation
    {
        return new SubtractAggregation($expression1, $expression2);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     */
    public static function sum(Decimal128|Int64|ResolvesToNumber|float|int ...$expression): SumAggregation
    {
        return new SumAggregation(...$expression);
    }

    /**
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $branches An array of control branch documents. Each branch is a document with the following fields:
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
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $tan takes any valid expression that resolves to a number. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the result to radians.
     * By default $tan returns values as a double. $tan can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public static function tan(Decimal128|Int64|ResolvesToNumber|float|int $expression): TanAggregation
    {
        return new TanAggregation($expression);
    }

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $tanh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $tanh returns values as a double. $tanh can also return values as a 128-bit decimal if the expression resolves to a 128-bit decimal value.
     */
    public static function tanh(Decimal128|Int64|ResolvesToNumber|float|int $expression): TanhAggregation
    {
        return new TanhAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function toBool(mixed $expression): ToBoolAggregation
    {
        return new ToBoolAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function toDate(mixed $expression): ToDateAggregation
    {
        return new ToDateAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function toDecimal(mixed $expression): ToDecimalAggregation
    {
        return new ToDecimalAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function toDouble(mixed $expression): ToDoubleAggregation
    {
        return new ToDoubleAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function toInt(mixed $expression): ToIntAggregation
    {
        return new ToIntAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function toLong(mixed $expression): ToLongAggregation
    {
        return new ToLongAggregation($expression);
    }

    /**
     * @param ResolvesToString|non-empty-string $expression
     */
    public static function toLower(ResolvesToString|string $expression): ToLowerAggregation
    {
        return new ToLowerAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function toObjectId(mixed $expression): ToObjectIdAggregation
    {
        return new ToObjectIdAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function toString(mixed $expression): ToStringAggregation
    {
        return new ToStringAggregation($expression);
    }

    /**
     * @param ResolvesToString|non-empty-string $expression
     */
    public static function toUpper(ResolvesToString|string $expression): ToUpperAggregation
    {
        return new ToUpperAggregation($expression);
    }

    /**
     * @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public static function top(stdClass|array $sortBy, mixed $output): TopAggregation
    {
        return new TopAggregation($sortBy, $output);
    }

    /**
     * @param Int64|ResolvesToInt|int $n limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group.
     * @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public static function topN(Int64|ResolvesToInt|int $n, stdClass|array $sortBy, mixed $output): TopNAggregation
    {
        return new TopNAggregation($n, $sortBy, $output);
    }

    /**
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
     * @param Int64|ResolvesToTimestamp|int $expression
     */
    public static function tsIncrement(Int64|ResolvesToTimestamp|int $expression): TsIncrementAggregation
    {
        return new TsIncrementAggregation($expression);
    }

    /**
     * @param Int64|ResolvesToTimestamp|int $expression
     */
    public static function tsSecond(Int64|ResolvesToTimestamp|int $expression): TsSecondAggregation
    {
        return new TsSecondAggregation($expression);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function type(mixed $expression): TypeAggregation
    {
        return new TypeAggregation($expression);
    }

    /**
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
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function week(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): WeekAggregation
    {
        return new WeekAggregation($date, $timezone);
    }

    /**
     * @param DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public static function year(
        \UTCDateTime|DateTimeInterface|Int64|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ): YearAggregation
    {
        return new YearAggregation($date, $timezone);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $inputs An array of expressions that resolve to arrays. The elements of these input arrays combine to form the arrays of the output array.
     * If any of the inputs arrays resolves to a value of null or refers to a missing field, $zip returns null.
     * If any of the inputs arrays does not resolve to an array or null nor refers to a missing field, $zip returns an error.
     * @param bool $useLongestLength A boolean which specifies whether the length of the longest array determines the number of arrays in the output array.
     * The default value is false: the shortest array length determines the number of arrays in the output array.
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $defaults An array of default element values to use if the input arrays have different lengths. You must specify useLongestLength: true along with this field, or else $zip will return an error.
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
