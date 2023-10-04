<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;

class SearchMetaStage implements StageInterface
{
    public const NAME = '$searchMeta';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|Serializable|array|object $meta */
    public array|object $meta;

    /**
     * @param Document|Serializable|array|object $meta
     */
    public function __construct(array|object $meta)
    {
        $this->meta = $meta;
    }
}
