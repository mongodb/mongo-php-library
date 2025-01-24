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
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Returns statistics regarding a collection or view.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/
 * @internal
 */
final class CollStatsStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$collStats';

    public const PROPERTIES = [
        'latencyStats' => 'latencyStats',
        'storageStats' => 'storageStats',
        'count' => 'count',
        'queryExecStats' => 'queryExecStats',
    ];

    /** @var Optional|Document|Serializable|array|stdClass $latencyStats */
    public readonly Optional|Document|Serializable|stdClass|array $latencyStats;

    /** @var Optional|Document|Serializable|array|stdClass $storageStats */
    public readonly Optional|Document|Serializable|stdClass|array $storageStats;

    /** @var Optional|Document|Serializable|array|stdClass $count */
    public readonly Optional|Document|Serializable|stdClass|array $count;

    /** @var Optional|Document|Serializable|array|stdClass $queryExecStats */
    public readonly Optional|Document|Serializable|stdClass|array $queryExecStats;

    /**
     * @param Optional|Document|Serializable|array|stdClass $latencyStats
     * @param Optional|Document|Serializable|array|stdClass $storageStats
     * @param Optional|Document|Serializable|array|stdClass $count
     * @param Optional|Document|Serializable|array|stdClass $queryExecStats
     */
    public function __construct(
        Optional|Document|Serializable|stdClass|array $latencyStats = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $storageStats = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $count = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $queryExecStats = Optional::Undefined,
    ) {
        $this->latencyStats = $latencyStats;
        $this->storageStats = $storageStats;
        $this->count = $count;
        $this->queryExecStats = $queryExecStats;
    }
}
