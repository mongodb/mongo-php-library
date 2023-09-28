<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToObject;

class ProjectStage implements StageInterface
{
    public const NAME = '$project';
    public const ENCODE = 'single';

    public array|object $specifications;

    /**
     * @param Document|ResolvesToObject|Serializable|array|object $specifications
     */
    public function __construct(array|object $specifications)
    {
        $this->specifications = $specifications;
    }
}
