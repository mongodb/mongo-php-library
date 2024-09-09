<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Returns different types of metadata result documents for the Atlas Search query against an Atlas collection.
 * NOTE: $searchMeta is only available for MongoDB Atlas clusters running MongoDB v4.4.9 or higher, and is not available for self-managed deployments.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/searchMeta/
 */
class SearchMetaStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var Document|Serializable|array|stdClass $meta */
    public readonly Document|Serializable|stdClass|array $meta;

    /**
     * @param Document|Serializable|array|stdClass $meta
     */
    public function __construct(Document|Serializable|stdClass|array $meta)
    {
        $this->meta = $meta;
    }

    public function getOperator(): string
    {
        return '$searchMeta';
    }
}
