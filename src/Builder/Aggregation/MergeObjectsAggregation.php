<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToObject;
use stdClass;

/**
 * Combines multiple documents into a single document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/
 */
class MergeObjectsAggregation implements AccumulatorInterface
{
    public const NAME = '$mergeObjects';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<Document|ResolvesToObject|Serializable|array|stdClass> ...$document Any valid expression that resolves to a document. */
    public array $document;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass ...$document Any valid expression that resolves to a document.
     * @no-named-arguments
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array ...$document)
    {
        if (\count($document) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $document, got %d.', 1, \count($document)));
        }
        if (! \array_is_list($document)) {
            throw new \InvalidArgumentException('Expected $document arguments to be a list of Document|ResolvesToObject|Serializable|array|stdClass, named arguments are not supported');
        }
        $this->document = $document;
    }
}
