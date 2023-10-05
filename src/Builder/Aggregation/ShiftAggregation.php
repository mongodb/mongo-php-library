<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Returns the value from an expression applied to a document in a specified position relative to the current document in the $setWindowFields stage partition.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/
 */
class ShiftAggregation implements ExpressionInterface
{
    public const NAME = '$shift';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ExpressionInterface|mixed $output Specifies an expression to evaluate and return in the output. */
    public mixed $output;

    /**
     * @param Int64|int $by Specifies an integer with a numeric document position relative to the current document in the output.
     * For example:
     * 1 specifies the document position after the current document.
     * -1 specifies the document position before the current document.
     * -2 specifies the document position that is two positions before the current document.
     */
    public Int64|int $by;

    /**
     * @param ExpressionInterface|mixed $default Specifies an optional default expression to evaluate if the document position is outside of the implicit $setWindowFields stage window. The implicit window contains all the documents in the partition.
     * The default expression must evaluate to a constant value.
     * If you do not specify a default expression, $shift returns null for documents whose positions are outside of the implicit $setWindowFields stage window.
     */
    public mixed $default;

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
    public function __construct(mixed $output, Int64|int $by, mixed $default)
    {
        $this->output = $output;
        $this->by = $by;
        $this->default = $default;
    }
}
