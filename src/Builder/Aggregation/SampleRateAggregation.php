<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToFloat;

/**
 * Randomly select documents at a given rate. Although the exact number of documents selected varies on each run, the quantity chosen approximates the sample rate expressed as a percentage of the total number of documents.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sampleRate/
 */
class SampleRateAggregation implements ExpressionInterface
{
    public const NAME = '$sampleRate';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @param Int64|ResolvesToFloat|float|int $rate The selection process uses a uniform random distribution. The sample rate is a floating point number between 0 and 1, inclusive, which represents the probability that a given document will be selected as it passes through the pipeline.
     * For example, a sample rate of 0.33 selects roughly one document in three.
     */
    public Int64|ResolvesToFloat|float|int $rate;

    /**
     * @param Int64|ResolvesToFloat|float|int $rate The selection process uses a uniform random distribution. The sample rate is a floating point number between 0 and 1, inclusive, which represents the probability that a given document will be selected as it passes through the pipeline.
     * For example, a sample rate of 0.33 selects roughly one document in three.
     */
    public function __construct(Int64|ResolvesToFloat|float|int $rate)
    {
        $this->rate = $rate;
    }
}
