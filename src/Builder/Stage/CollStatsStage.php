<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;

class CollStatsStage implements StageInterface
{
    public const NAME = '$collStats';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|Serializable|array|object $config */
    public array|object $config;

    /**
     * @param Document|Serializable|array|object $config
     */
    public function __construct(array|object $config)
    {
        $this->config = $config;
    }
}
