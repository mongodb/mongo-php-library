<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Combines multiple documents into a single document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/
 */
class MergeObjectsAccumulator implements AccumulatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var Document|ResolvesToObject|Serializable|array|stdClass $document Any valid expression that resolves to a document. */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array $document;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass $document Any valid expression that resolves to a document.
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array $document)
    {
        $this->document = $document;
    }

    public function getOperator(): string
    {
        return '$mergeObjects';
    }
}
