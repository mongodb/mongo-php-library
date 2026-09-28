<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Binary;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Returns the subtype of a given value as an integer. In MongoDB 8.3, the only expression
 * that contains a subtype is a BinData expression.
 *
 * New in MongoDB 8.3
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtype/
 * @internal
 */
final class SubtypeOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$subtype';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var Binary|ResolvesToBinData|string $expression An expression that resolves to a BinData value. */
    public readonly Binary|ResolvesToBinData|string $expression;

    /** @param Binary|ResolvesToBinData|string $expression An expression that resolves to a BinData value. */
    public function __construct(Binary|ResolvesToBinData|string $expression)
    {
        $this->expression = $expression;
    }
}
