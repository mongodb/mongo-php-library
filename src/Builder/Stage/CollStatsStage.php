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
 * Returns statistics regarding a collection or view.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/
 */
class CollStatsStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var Document|Serializable|array|stdClass $config */
    public readonly Document|Serializable|stdClass|array $config;

    /**
     * @param Document|Serializable|array|stdClass $config
     */
    public function __construct(Document|Serializable|stdClass|array $config)
    {
        $this->config = $config;
    }

    public function getOperator(): string
    {
        return '$collStats';
    }
}
