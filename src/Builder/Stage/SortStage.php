<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Serializable;

class SortStage implements StageInterface
{
    public const NAME = '$sort';
    public const ENCODE = 'single';

    /** @param list<Document|Int64|Serializable|array|int|object> ...$sortSpecification */
    public array $sortSpecification;

    /**
     * @param Document|Int64|Serializable|array|int|object $sortSpecification
     */
    public function __construct(array|int|object ...$sortSpecification)
    {
        if (\count($sortSpecification) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', 1, \count($sortSpecification)));
        }

        $this->sortSpecification = $sortSpecification;
    }
}
