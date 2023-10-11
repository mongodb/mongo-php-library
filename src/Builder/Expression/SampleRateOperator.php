<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Randomly select documents at a given rate. Although the exact number of documents selected varies on each run, the quantity chosen approximates the sample rate expressed as a percentage of the total number of documents.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sampleRate/
 */
class SampleRateOperator implements ResolvesToAny
{
    public const NAME = '$sampleRate';
    public const ENCODE = Encode::Single;

    /**
     * @param Int64|ResolvesToDouble|float|int $rate The selection process uses a uniform random distribution. The sample rate is a floating point number between 0 and 1, inclusive, which represents the probability that a given document will be selected as it passes through the pipeline.
     * For example, a sample rate of 0.33 selects roughly one document in three.
     */
    public Int64|ResolvesToDouble|float|int $rate;

    /**
     * @param Int64|ResolvesToDouble|float|int $rate The selection process uses a uniform random distribution. The sample rate is a floating point number between 0 and 1, inclusive, which represents the probability that a given document will be selected as it passes through the pipeline.
     * For example, a sample rate of 0.33 selects roughly one document in three.
     */
    public function __construct(Int64|ResolvesToDouble|float|int $rate)
    {
        $this->rate = $rate;
    }
}
