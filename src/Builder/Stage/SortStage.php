<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToObject;

class SortStage
{
    public array|object $sortSpecification;

    /** @param Document|ResolvesToObject|Serializable|array|object $sortSpecification */
    public function __construct(array|object $sortSpecification)
    {
        $this->sortSpecification = $sortSpecification;
    }
}
