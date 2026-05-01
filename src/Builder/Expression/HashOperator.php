<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Generates and returns a binary hash value (BinData) from a UTF-8 string or binary data. Use $hash in an aggregation
 * pipeline to compute binary hashes for storage, verification, or comparison. To get a hexadecimal string instead of
 * binary data, use $hexHash.
 *
 * New in MongoDB 8.3
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hash/
 * @internal
 */
final class HashOperator implements ResolvesToBinData, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$hash';
    public const PROPERTIES = ['input' => 'input', 'algorithm' => 'algorithm'];

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input;

    /** @var string $algorithm */
    public readonly string $algorithm;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input
     * @param string $algorithm
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input,
        string $algorithm,
    ) {
        $this->input = $input;
        $this->algorithm = $algorithm;
    }
}
