<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Returns different types of metadata result documents for the Atlas Search query against an Atlas collection.
 * NOTE: $searchMeta is only available for MongoDB Atlas clusters running MongoDB v4.4.9 or higher, and is not available for self-managed deployments.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/searchMeta/
 */
readonly class SearchMetaStage implements StageInterface
{
    public const NAME = '$searchMeta';
    public const ENCODE = Encode::Single;

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
