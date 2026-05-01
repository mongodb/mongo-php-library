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
 * Generates and returns an uppercase hexadecimal string representation of a hash value from a UTF-8 string or binary
 * data. Use $hexHash in an aggregation pipeline to compute hex-encoded hashes for storage, verification, or comparison.
 * To get binary data instead of a hexadecimal string, use $hash.
 *
 * New in MongoDB 8.3
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hexHash/
 * @internal
 */
final class HexHashOperator implements ResolvesToString, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$hexHash';
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
