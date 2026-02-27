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
 * Returns the size of a given string or binary data value's content in bytes.
 *
 * New in MongoDB 4.4
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/binarySize/
 * @internal
 */
final class BinarySizeOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$binarySize';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression */
    public readonly Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression;

    /**
     * @param Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression
     */
    public function __construct(Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression)
    {
        $this->expression = $expression;
    }
}
