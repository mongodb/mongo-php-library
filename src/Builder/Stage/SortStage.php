<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Int64;

class SortStage implements StageInterface
{
    public const NAME = '$sort';
    public const ENCODE = 'single';

    /** @param list<Int64|int> ...$sortSpecification */
    public array $sortSpecification;

    /**
     * @param Int64|int $sortSpecification
     */
    public function __construct(Int64|int ...$sortSpecification)
    {
        if (\count($sortSpecification) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', 1, \count($sortSpecification)));
        }

        $this->sortSpecification = $sortSpecification;
    }
}
