<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Javascript;
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
use function is_string;
use function str_starts_with;

/**
 * Defines a custom accumulator function.
 * New in MongoDB 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/accumulator/
 * @internal
 */
final class AccumulatorAccumulator implements AccumulatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$accumulator';

    public const PROPERTIES = [
        'init' => 'init',
        'accumulate' => 'accumulate',
        'accumulateArgs' => 'accumulateArgs',
        'merge' => 'merge',
        'lang' => 'lang',
        'initArgs' => 'initArgs',
        'finalize' => 'finalize',
    ];

    /** @var Javascript|string $init Function used to initialize the state. The init function receives its arguments from the initArgs array expression. You can specify the function definition as either BSON type Code or String. */
    public readonly Javascript|string $init;

    /** @var Javascript|string $accumulate Function used to accumulate documents. The accumulate function receives its arguments from the current state and accumulateArgs array expression. The result of the accumulate function becomes the new state. You can specify the function definition as either BSON type Code or String. */
    public readonly Javascript|string $accumulate;

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $accumulateArgs Arguments passed to the accumulate function. You can use accumulateArgs to specify what field value(s) to pass to the accumulate function. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $accumulateArgs;

    /** @var Javascript|string $merge Function used to merge two internal states. merge must be either a String or Code BSON type. merge returns the combined result of the two merged states. For information on when the merge function is called, see Merge Two States with $merge. */
    public readonly Javascript|string $merge;

    /** @var string $lang The language used in the $accumulator code. */
    public readonly string $lang;

    /** @var Optional|BSONArray|PackedArray|ResolvesToArray|array|string $initArgs Arguments passed to the init function. */
    public readonly Optional|PackedArray|ResolvesToArray|BSONArray|array|string $initArgs;

    /** @var Optional|Javascript|string $finalize Function used to update the result of the accumulation. */
    public readonly Optional|Javascript|string $finalize;

    /**
     * @param Javascript|string $init Function used to initialize the state. The init function receives its arguments from the initArgs array expression. You can specify the function definition as either BSON type Code or String.
     * @param Javascript|string $accumulate Function used to accumulate documents. The accumulate function receives its arguments from the current state and accumulateArgs array expression. The result of the accumulate function becomes the new state. You can specify the function definition as either BSON type Code or String.
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $accumulateArgs Arguments passed to the accumulate function. You can use accumulateArgs to specify what field value(s) to pass to the accumulate function.
     * @param Javascript|string $merge Function used to merge two internal states. merge must be either a String or Code BSON type. merge returns the combined result of the two merged states. For information on when the merge function is called, see Merge Two States with $merge.
     * @param string $lang The language used in the $accumulator code.
     * @param Optional|BSONArray|PackedArray|ResolvesToArray|array|string $initArgs Arguments passed to the init function.
     * @param Optional|Javascript|string $finalize Function used to update the result of the accumulation.
     */
    public function __construct(
        Javascript|string $init,
        Javascript|string $accumulate,
        PackedArray|ResolvesToArray|BSONArray|array|string $accumulateArgs,
        Javascript|string $merge,
        string $lang,
        Optional|PackedArray|ResolvesToArray|BSONArray|array|string $initArgs = Optional::Undefined,
        Optional|Javascript|string $finalize = Optional::Undefined,
    ) {
        if (is_string($init)) {
            $init = new Javascript($init);
        }

        $this->init = $init;
        if (is_string($accumulate)) {
            $accumulate = new Javascript($accumulate);
        }

        $this->accumulate = $accumulate;
        if (is_string($accumulateArgs) && ! str_starts_with($accumulateArgs, '$')) {
            throw new InvalidArgumentException('Argument $accumulateArgs can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($accumulateArgs) && ! array_is_list($accumulateArgs)) {
            throw new InvalidArgumentException('Expected $accumulateArgs argument to be a list, got an associative array.');
        }

        $this->accumulateArgs = $accumulateArgs;
        if (is_string($merge)) {
            $merge = new Javascript($merge);
        }

        $this->merge = $merge;
        $this->lang = $lang;
        if (is_string($initArgs) && ! str_starts_with($initArgs, '$')) {
            throw new InvalidArgumentException('Argument $initArgs can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($initArgs) && ! array_is_list($initArgs)) {
            throw new InvalidArgumentException('Expected $initArgs argument to be a list, got an associative array.');
        }

        $this->initArgs = $initArgs;
        if (is_string($finalize)) {
            $finalize = new Javascript($finalize);
        }

        $this->finalize = $finalize;
    }
}
