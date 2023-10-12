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
 * Returns statistics regarding a collection or view.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/
 */
readonly class CollStatsStage implements StageInterface
{
    public const NAME = '$collStats';
    public const ENCODE = Encode::Single;

    /** @param Document|Serializable|array|stdClass $config */
    public Document|Serializable|stdClass|array $config;

    /**
     * @param Document|Serializable|array|stdClass $config
     */
    public function __construct(Document|Serializable|stdClass|array $config)
    {
        $this->config = $config;
    }
}
