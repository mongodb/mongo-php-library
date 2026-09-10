<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Converts a string to lowercase. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLower/
 * @internal
 */
final class ToLowerOperator implements ResolvesToString, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$toLower';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var ResolvesToString|string $expression */
    public readonly ResolvesToString|string $expression;

    /** @param ResolvesToString|string $expression */
    public function __construct(ResolvesToString|string $expression)
    {
        $this->expression = $expression;
    }
}
