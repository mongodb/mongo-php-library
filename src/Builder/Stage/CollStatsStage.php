<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use stdClass;

class CollStatsStage implements StageInterface
{
    public const NAME = '$collStats';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

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
