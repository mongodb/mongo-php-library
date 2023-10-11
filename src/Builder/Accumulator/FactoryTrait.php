<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Accumulator;

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
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Expression\ResolvesToObject;
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
     * Defines a custom accumulator function.
     * New in version 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/accumulator/
     * @param non-empty-string $init Function used to initialize the state. The init function receives its arguments from the initArgs array expression. You can specify the function definition as either BSON type Code or String.
     * @param non-empty-string $accumulate Function used to accumulate documents. The accumulate function receives its arguments from the current state and accumulateArgs array expression. The result of the accumulate function becomes the new state. You can specify the function definition as either BSON type Code or String.
     * @param BSONArray|PackedArray|ResolvesToArray|array $accumulateArgs Arguments passed to the accumulate function. You can use accumulateArgs to specify what field value(s) to pass to the accumulate function.
     * @param non-empty-string $merge Function used to merge two internal states. merge must be either a String or Code BSON type. merge returns the combined result of the two merged states. For information on when the merge function is called, see Merge Two States with $merge.
     * @param non-empty-string $lang The language used in the $accumulator code.
     * @param Optional|BSONArray|PackedArray|ResolvesToArray|array $initArgs Arguments passed to the init function.
     * @param Optional|non-empty-string $finalize Function used to update the result of the accumulation.
     */
    public static function accumulator(
        string $init,
        string $accumulate,
        PackedArray|ResolvesToArray|BSONArray|array $accumulateArgs,
        string $merge,
        string $lang,
        Optional|PackedArray|ResolvesToArray|BSONArray|array $initArgs = Optional::Undefined,
        Optional|string $finalize = Optional::Undefined,
    ): AccumulatorAccumulator
    {
        return new AccumulatorAccumulator($init, $accumulate, $accumulateArgs, $merge, $lang, $initArgs, $finalize);
    }

    /**
     * Returns an array of unique expression values for each group. Order of the array elements is undefined.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addToSet/
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public static function addToSet(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ): AddToSetAccumulator
    {
        return new AddToSetAccumulator($expression);
    }

    /**
     * Returns an average of numerical values. Ignores non-numeric values.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression
     */
    public static function avg(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression): AvgAccumulator
    {
        return new AvgAccumulator($expression);
    }

    /**
     * Returns the bottom element within a group according to the specified sort order.
     * New in version 5.2: Available in the $group and $setWindowFields stages.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom/
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression.
     */
    public static function bottom(
        Document|Serializable|stdClass|array $sortBy,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $output,
    ): BottomAccumulator
    {
        return new BottomAccumulator($sortBy, $output);
    }

    /**
     * Returns an aggregation of the bottom n elements within a group, according to the specified sort order. If the group contains fewer than n elements, $bottomN returns all elements in the group.
     * New in version 5.2.
     * Available in the $group and $setWindowFields stages.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/
     * @param ResolvesToInt|int $n Limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group.
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression.
     */
    public static function bottomN(
        ResolvesToInt|int $n,
        Document|Serializable|stdClass|array $sortBy,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $output,
    ): BottomNAccumulator
    {
        return new BottomNAccumulator($n, $sortBy, $output);
    }

    /**
     * Returns the number of documents in the group or window.
     * Distinct from the $count pipeline stage.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count-accumulator/
     */
    public static function count(): CountAccumulator
    {
        return new CountAccumulator();
    }

    /**
     * Returns the population covariance of two numeric expressions.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covariancePop/
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression1
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression2
     */
    public static function covariancePop(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression1,
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression2,
    ): CovariancePopAccumulator
    {
        return new CovariancePopAccumulator($expression1, $expression2);
    }

    /**
     * Returns the sample covariance of two numeric expressions.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covarianceSamp/
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression1
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression2
     */
    public static function covarianceSamp(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression1,
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression2,
    ): CovarianceSampAccumulator
    {
        return new CovarianceSampAccumulator($expression1, $expression2);
    }

    /**
     * Returns the document position (known as the rank) relative to other documents in the $setWindowFields stage partition. There are no gaps in the ranks. Ties receive the same rank.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/denseRank/
     */
    public static function denseRank(): DenseRankAccumulator
    {
        return new DenseRankAccumulator();
    }

    /**
     * Returns the average rate of change within the specified window.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/derivative/
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToInt|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public static function derivative(
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToInt|ResolvesToNumber|float|int $input,
        Optional|string $unit = Optional::Undefined,
    ): DerivativeAccumulator
    {
        return new DerivativeAccumulator($input, $unit);
    }

    /**
     * Returns the position of a document (known as the document number) in the $setWindowFields stage partition. Ties result in different adjacent document numbers.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documentNumber/
     */
    public static function documentNumber(): DocumentNumberAccumulator
    {
        return new DocumentNumberAccumulator();
    }

    /**
     * Returns the exponential moving average for the numeric expression.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/expMovingAvg/
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input
     * @param Optional|int $N An integer that specifies the number of historical documents that have a significant mathematical weight in the exponential moving average calculation, with the most recent documents contributing the most weight.
     * You must specify either N or alpha. You cannot specify both.
     * The N value is used in this formula to calculate the current result based on the expression value from the current document being read and the previous result of the calculation:
     * @param Optional|Int64|float|int $alpha A double that specifies the exponential decay value to use in the exponential moving average calculation. A higher alpha value assigns a lower mathematical significance to previous results from the calculation.
     * You must specify either N or alpha. You cannot specify both.
     */
    public static function expMovingAvg(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input,
        Optional|int $N = Optional::Undefined,
        Optional|Int64|float|int $alpha = Optional::Undefined,
    ): ExpMovingAvgAccumulator
    {
        return new ExpMovingAvgAccumulator($input, $N, $alpha);
    }

    /**
     * Returns the result of an expression for the first document in a group or window.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/first/
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public static function first(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ): FirstAccumulator
    {
        return new FirstAccumulator($expression);
    }

    /**
     * Returns a specified number of elements from the beginning of an array. Distinct from the $firstN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return n elements.
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     */
    public static function firstN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToInt|int $n,
    ): FirstNAccumulator
    {
        return new FirstNAccumulator($input, $n);
    }

    /**
     * Returns the result of an expression for the last document in a group or window.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public static function last(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ): LastAccumulator
    {
        return new LastAccumulator($expression);
    }

    /**
     * Returns a specified number of elements from the end of an array. Distinct from the $lastN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return n elements.
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     */
    public static function lastN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToInt|int $n,
    ): LastNAccumulator
    {
        return new LastNAccumulator($input, $n);
    }

    /**
     * Returns the maximum value that results from applying an expression to each document.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public static function max(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ): MaxAccumulator
    {
        return new MaxAccumulator($expression);
    }

    /**
     * Returns the n largest values in an array. Distinct from the $maxN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return the maximal n elements.
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns.
     */
    public static function maxN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToInt|int $n,
    ): MaxNAccumulator
    {
        return new MaxNAccumulator($input, $n);
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
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input $median calculates the 50th percentile value of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $median calculation ignores it.
     * @param non-empty-string $method The method that mongod uses to calculate the 50th percentile value. The method must be 'approximate'.
     */
    public static function median(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input,
        string $method,
    ): MedianAccumulator
    {
        return new MedianAccumulator($input, $method);
    }

    /**
     * Combines multiple documents into a single document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/
     * @param Document|ResolvesToObject|Serializable|array|stdClass ...$document Any valid expression that resolves to a document.
     */
    public static function mergeObjects(
        Document|Serializable|ResolvesToObject|stdClass|array ...$document,
    ): MergeObjectsAccumulator
    {
        return new MergeObjectsAccumulator(...$document);
    }

    /**
     * Returns the minimum value that results from applying an expression to each document.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public static function min(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ): MinAccumulator
    {
        return new MinAccumulator($expression);
    }

    /**
     * Returns the n smallest values in an array. Distinct from the $minN accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return the maximal n elements.
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns.
     */
    public static function minN(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToInt|int $n,
    ): MinNAccumulator
    {
        return new MinNAccumulator($input, $n);
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
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input $percentile calculates the percentile values of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $percentile calculation ignores it.
     * @param BSONArray|PackedArray|ResolvesToArray|array $p $percentile calculates a percentile value for each element in p. The elements represent percentages and must evaluate to numeric values in the range 0.0 to 1.0, inclusive.
     * $percentile returns results in the same order as the elements in p.
     * @param non-empty-string $method The method that mongod uses to calculate the percentile value. The method must be 'approximate'.
     */
    public static function percentile(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input,
        PackedArray|ResolvesToArray|BSONArray|array $p,
        string $method,
    ): PercentileAccumulator
    {
        return new PercentileAccumulator($input, $p, $method);
    }

    /**
     * Returns an array of values that result from applying an expression to each document.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/push/
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public static function push(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ): PushAccumulator
    {
        return new PushAccumulator($expression);
    }

    /**
     * Returns the value from an expression applied to a document in a specified position relative to the current document in the $setWindowFields stage partition.
     * New in version 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $output Specifies an expression to evaluate and return in the output.
     * @param int $by Specifies an integer with a numeric document position relative to the current document in the output.
     * For example:
     * 1 specifies the document position after the current document.
     * -1 specifies the document position before the current document.
     * -2 specifies the document position that is two positions before the current document.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $default Specifies an optional default expression to evaluate if the document position is outside of the implicit $setWindowFields stage window. The implicit window contains all the documents in the partition.
     * The default expression must evaluate to a constant value.
     * If you do not specify a default expression, $shift returns null for documents whose positions are outside of the implicit $setWindowFields stage window.
     */
    public static function shift(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $output,
        int $by,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $default,
    ): ShiftAccumulator
    {
        return new ShiftAccumulator($output, $by, $default);
    }

    /**
     * Calculates the population standard deviation of the input values. Use if the values encompass the entire population of data you want to represent and do not wish to generalize about a larger population. $stdDevPop ignores non-numeric values.
     * If the values represent only a sample of a population of data from which to generalize about the population, use $stdDevSamp instead.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression
     */
    public static function stdDevPop(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression,
    ): StdDevPopAccumulator
    {
        return new StdDevPopAccumulator($expression);
    }

    /**
     * Calculates the sample standard deviation of the input values. Use if the values encompass a sample of a population of data from which to generalize about the population. $stdDevSamp ignores non-numeric values.
     * If the values represent the entire population of data or you do not wish to generalize about a larger population, use $stdDevPop instead.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression
     */
    public static function stdDevSamp(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression,
    ): StdDevSampAccumulator
    {
        return new StdDevSampAccumulator($expression);
    }

    /**
     * Returns a sum of numerical values. Ignores non-numeric values.
     * Changed in version 5.0: Available in the $setWindowFields stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression
     */
    public static function sum(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression): SumAccumulator
    {
        return new SumAccumulator($expression);
    }

    /**
     * Returns the top element within a group according to the specified sort order.
     * New in version 5.2.
     *
     * Available in the $group and $setWindowFields stages.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/top/
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression.
     */
    public static function top(
        Document|Serializable|stdClass|array $sortBy,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $output,
    ): TopAccumulator
    {
        return new TopAccumulator($sortBy, $output);
    }

    /**
     * Returns an aggregation of the top n fields within a group, according to the specified sort order.
     * New in version 5.2.
     *
     * Available in the $group and $setWindowFields stages.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/
     * @param ResolvesToInt|int $n limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group.
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression.
     */
    public static function topN(
        ResolvesToInt|int $n,
        Document|Serializable|stdClass|array $sortBy,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $output,
    ): TopNAccumulator
    {
        return new TopNAccumulator($n, $sortBy, $output);
    }
}
