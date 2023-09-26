<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Expression\ResolvesToSortSpecification;

class SortStage
{
    public array|object $sortSpecification;

    /** @param ResolvesToSortSpecification|array|object $sortSpecification */
    public function __construct(array|object $sortSpecification)
    {
        $this->sortSpecification = $sortSpecification;
    }
}
