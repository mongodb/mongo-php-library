<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Binary;
use MongoDB\Builder\Type\Encode;

/**
 * Returns the size of a given string or binary data value's content in bytes.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/binarySize/
 */
readonly class BinarySizeOperator implements ResolvesToInt
{
    public const NAME = '$binarySize';
    public const ENCODE = Encode::Single;

    /** @param Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|non-empty-string|null $expression */
    public Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression;

    /**
     * @param Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|non-empty-string|null $expression
     */
    public function __construct(Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $expression)
    {
        $this->expression = $expression;
    }
}
