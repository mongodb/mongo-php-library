<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

/**
 * Returns the value from an expression applied to a document in a specified position relative to the current document in the $setWindowFields stage partition.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/
 */
readonly class ShiftAccumulator implements WindowInterface
{
    public const NAME = '$shift';
    public const ENCODE = Encode::Object;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $output Specifies an expression to evaluate and return in the output. */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output;

    /**
     * @param int $by Specifies an integer with a numeric document position relative to the current document in the output.
     * For example:
     * 1 specifies the document position after the current document.
     * -1 specifies the document position before the current document.
     * -2 specifies the document position that is two positions before the current document.
     */
    public int $by;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $default Specifies an optional default expression to evaluate if the document position is outside of the implicit $setWindowFields stage window. The implicit window contains all the documents in the partition.
     * The default expression must evaluate to a constant value.
     * If you do not specify a default expression, $shift returns null for documents whose positions are outside of the implicit $setWindowFields stage window.
     */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $default;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $output Specifies an expression to evaluate and return in the output.
     * @param int $by Specifies an integer with a numeric document position relative to the current document in the output.
     * For example:
     * 1 specifies the document position after the current document.
     * -1 specifies the document position before the current document.
     * -2 specifies the document position that is two positions before the current document.
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $default Specifies an optional default expression to evaluate if the document position is outside of the implicit $setWindowFields stage window. The implicit window contains all the documents in the partition.
     * The default expression must evaluate to a constant value.
     * If you do not specify a default expression, $shift returns null for documents whose positions are outside of the implicit $setWindowFields stage window.
     */
    public function __construct(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output,
        int $by,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $default,
    ) {
        $this->output = $output;
        $this->by = $by;
        $this->default = $default;
    }
}
