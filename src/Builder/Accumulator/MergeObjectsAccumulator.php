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
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_is_list;

/**
 * Combines multiple documents into a single document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/
 */
class MergeObjectsAccumulator implements AccumulatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var list<Document|ResolvesToObject|Serializable|array|stdClass> ...$document Any valid expression that resolves to a document. */
    public readonly array $document;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass ...$document Any valid expression that resolves to a document.
     * @no-named-arguments
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array ...$document)
    {
        if (\count($document) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $document, got %d.', 1, \count($document)));
        }
        if (! array_is_list($document)) {
            throw new InvalidArgumentException('Expected $document arguments to be a list (array), named arguments are not supported');
        }
        $this->document = $document;
    }

    public function getOperator(): string
    {
        return '$mergeObjects';
    }
}
