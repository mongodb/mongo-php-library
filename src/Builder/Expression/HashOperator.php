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

    /** @var Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $input */
    public readonly Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $input;

    /** @var string $algorithm */
    public readonly string $algorithm;

    /**
     * @param Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $input
     * @param string $algorithm
     */
    public function __construct(
        Binary|ResolvesToBinData|ResolvesToNull|ResolvesToString|null|string $input,
        string $algorithm,
    ) {
        $this->input = $input;
        $this->algorithm = $algorithm;
    }
}
