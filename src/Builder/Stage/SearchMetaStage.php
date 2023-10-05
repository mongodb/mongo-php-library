<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use stdClass;

class SearchMetaStage implements StageInterface
{
    public const NAME = '$searchMeta';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|Serializable|array|stdClass $meta */
    public Document|Serializable|stdClass|array $meta;

    /**
     * @param Document|Serializable|array|stdClass $meta
     */
    public function __construct(Document|Serializable|stdClass|array $meta)
    {
        $this->meta = $meta;
    }
}
