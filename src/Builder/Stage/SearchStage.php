<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;

class SearchStage implements StageInterface
{
    public const NAME = '$search';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|Serializable|array|object $search */
    public array|object $search;

    /**
     * @param Document|Serializable|array|object $search
     */
    public function __construct(array|object $search)
    {
        $this->search = $search;
    }
}
