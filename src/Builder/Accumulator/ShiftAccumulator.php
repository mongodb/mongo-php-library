<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

/**
 * Returns the value from an expression applied to a document in a specified position relative to the current document in the $setWindowFields stage partition.
 * New in MongoDB 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/
 * @internal
 */
final class ShiftAccumulator implements WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$shift';
    public const PROPERTIES = ['output' => 'output', 'by' => 'by', 'default' => 'default'];

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $output Specifies an expression to evaluate and return in the output. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output;

    /**
     * @var int $by Specifies an integer with a numeric document position relative to the current document in the output.
     * For example:
     * 1 specifies the document position after the current document.
     * -1 specifies the document position before the current document.
     * -2 specifies the document position that is two positions before the current document.
     */
    public readonly int $by;

    /**
     * @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $default Specifies an optional default expression to evaluate if the document position is outside of the implicit $setWindowFields stage window. The implicit window contains all the documents in the partition.
     * The default expression must evaluate to a constant value.
     * If you do not specify a default expression, $shift returns null for documents whose positions are outside of the implicit $setWindowFields stage window.
     */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $default;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $output Specifies an expression to evaluate and return in the output.
     * @param int $by Specifies an integer with a numeric document position relative to the current document in the output.
     * For example:
     * 1 specifies the document position after the current document.
     * -1 specifies the document position before the current document.
     * -2 specifies the document position that is two positions before the current document.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $default Specifies an optional default expression to evaluate if the document position is outside of the implicit $setWindowFields stage window. The implicit window contains all the documents in the partition.
     * The default expression must evaluate to a constant value.
     * If you do not specify a default expression, $shift returns null for documents whose positions are outside of the implicit $setWindowFields stage window.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output,
        int $by,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $default,
    ) {
        $this->output = $output;
        $this->by = $by;
        $this->default = $default;
    }
}
