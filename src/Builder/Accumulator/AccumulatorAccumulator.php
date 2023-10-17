<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Defines a custom accumulator function.
 * New in MongoDB 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/accumulator/
 */
class AccumulatorAccumulator implements AccumulatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var non-empty-string $init Function used to initialize the state. The init function receives its arguments from the initArgs array expression. You can specify the function definition as either BSON type Code or String. */
    public readonly string $init;

    /** @var non-empty-string $accumulate Function used to accumulate documents. The accumulate function receives its arguments from the current state and accumulateArgs array expression. The result of the accumulate function becomes the new state. You can specify the function definition as either BSON type Code or String. */
    public readonly string $accumulate;

    /** @var BSONArray|PackedArray|ResolvesToArray|array $accumulateArgs Arguments passed to the accumulate function. You can use accumulateArgs to specify what field value(s) to pass to the accumulate function. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array $accumulateArgs;

    /** @var non-empty-string $merge Function used to merge two internal states. merge must be either a String or Code BSON type. merge returns the combined result of the two merged states. For information on when the merge function is called, see Merge Two States with $merge. */
    public readonly string $merge;

    /** @var non-empty-string $lang The language used in the $accumulator code. */
    public readonly string $lang;

    /** @var Optional|BSONArray|PackedArray|ResolvesToArray|array $initArgs Arguments passed to the init function. */
    public readonly Optional|PackedArray|ResolvesToArray|BSONArray|array $initArgs;

    /** @var Optional|non-empty-string $finalize Function used to update the result of the accumulation. */
    public readonly Optional|string $finalize;

    /**
     * @param non-empty-string $init Function used to initialize the state. The init function receives its arguments from the initArgs array expression. You can specify the function definition as either BSON type Code or String.
     * @param non-empty-string $accumulate Function used to accumulate documents. The accumulate function receives its arguments from the current state and accumulateArgs array expression. The result of the accumulate function becomes the new state. You can specify the function definition as either BSON type Code or String.
     * @param BSONArray|PackedArray|ResolvesToArray|array $accumulateArgs Arguments passed to the accumulate function. You can use accumulateArgs to specify what field value(s) to pass to the accumulate function.
     * @param non-empty-string $merge Function used to merge two internal states. merge must be either a String or Code BSON type. merge returns the combined result of the two merged states. For information on when the merge function is called, see Merge Two States with $merge.
     * @param non-empty-string $lang The language used in the $accumulator code.
     * @param Optional|BSONArray|PackedArray|ResolvesToArray|array $initArgs Arguments passed to the init function.
     * @param Optional|non-empty-string $finalize Function used to update the result of the accumulation.
     */
    public function __construct(
        string $init,
        string $accumulate,
        PackedArray|ResolvesToArray|BSONArray|array $accumulateArgs,
        string $merge,
        string $lang,
        Optional|PackedArray|ResolvesToArray|BSONArray|array $initArgs = Optional::Undefined,
        Optional|string $finalize = Optional::Undefined,
    ) {
        $this->init = $init;
        $this->accumulate = $accumulate;
        if (is_array($accumulateArgs) && ! array_is_list($accumulateArgs)) {
            throw new InvalidArgumentException('Expected $accumulateArgs argument to be a list, got an associative array.');
        }

        $this->accumulateArgs = $accumulateArgs;
        $this->merge = $merge;
        $this->lang = $lang;
        if (is_array($initArgs) && ! array_is_list($initArgs)) {
            throw new InvalidArgumentException('Expected $initArgs argument to be a list, got an associative array.');
        }

        $this->initArgs = $initArgs;
        $this->finalize = $finalize;
    }

    public function getOperator(): string
    {
        return '$accumulator';
    }
}
