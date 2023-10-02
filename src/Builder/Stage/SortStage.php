<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

class SortStage implements StageInterface
{
    public const NAME = '$sort';
    public const ENCODE = 'single';

    /** @param list<int> ...$sortSpecification */
    public array $sortSpecification;

    /**
     * @param int $sortSpecification
     */
    public function __construct(int ...$sortSpecification)
    {
        if (\count($sortSpecification) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', 1, \count($sortSpecification)));
        }

        $this->sortSpecification = $sortSpecification;
    }
}
