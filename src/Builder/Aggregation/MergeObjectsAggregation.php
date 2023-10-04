<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToObject;

class MergeObjectsAggregation implements AccumulatorInterface
{
    public const NAME = '$mergeObjects';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<Document|ResolvesToObject|Serializable|array|object> ...$document Any valid expression that resolves to a document. */
    public array $document;

    /**
     * @param Document|ResolvesToObject|Serializable|array|object $document Any valid expression that resolves to a document.
     */
    public function __construct(array|object ...$document)
    {
        if (! \array_is_list($document)) {
            throw new \InvalidArgumentException('Expected $document arguments to be a list of Document|ResolvesToObject|Serializable|array|object, named arguments are not supported');
        }
        if (\count($document) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $document, got %d.', 1, \count($document)));
        }
        $this->document = $document;
    }
}
