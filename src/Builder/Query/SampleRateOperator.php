<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Randomly select documents at a given rate. Although the exact number of documents selected varies on each run, the quantity chosen approximates the sample rate expressed as a percentage of the total number of documents.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sampleRate/
 * @internal
 */
final class SampleRateOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$sampleRate';
    public const PROPERTIES = ['rate' => 'rate'];

    /**
     * @var Int64|ResolvesToDouble|float|int|string $rate The selection process uses a uniform random distribution. The sample rate is a floating point number between 0 and 1, inclusive, which represents the probability that a given document will be selected as it passes through the pipeline.
     * For example, a sample rate of 0.33 selects roughly one document in three.
     */
    public readonly Int64|ResolvesToDouble|float|int|string $rate;

    /**
     * @param Int64|ResolvesToDouble|float|int|string $rate The selection process uses a uniform random distribution. The sample rate is a floating point number between 0 and 1, inclusive, which represents the probability that a given document will be selected as it passes through the pipeline.
     * For example, a sample rate of 0.33 selects roughly one document in three.
     */
    public function __construct(Int64|ResolvesToDouble|float|int|string $rate)
    {
        if (is_string($rate) && ! str_starts_with($rate, '$')) {
            throw new InvalidArgumentException('Argument $rate can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->rate = $rate;
    }
}
