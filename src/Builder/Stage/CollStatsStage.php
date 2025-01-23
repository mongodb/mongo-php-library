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

    /** @var Optional|Document|Serializable|array|stdClass|string $latencyStats */
    public readonly Optional|Document|Serializable|stdClass|array|string $latencyStats;

    /** @var Optional|Document|Serializable|array|stdClass|string $storageStats */
    public readonly Optional|Document|Serializable|stdClass|array|string $storageStats;

    /** @var Optional|Document|Serializable|array|stdClass|string $count */
    public readonly Optional|Document|Serializable|stdClass|array|string $count;

    /** @var Optional|Document|Serializable|array|stdClass|string $queryExecStats */
    public readonly Optional|Document|Serializable|stdClass|array|string $queryExecStats;

    /**
     * @param Optional|Document|Serializable|array|stdClass|string $latencyStats
     * @param Optional|Document|Serializable|array|stdClass|string $storageStats
     * @param Optional|Document|Serializable|array|stdClass|string $count
     * @param Optional|Document|Serializable|array|stdClass|string $queryExecStats
     */
    public function __construct(
        Optional|Document|Serializable|stdClass|array|string $latencyStats = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array|string $storageStats = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array|string $count = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array|string $queryExecStats = Optional::Undefined,
    ) {
        $this->latencyStats = $latencyStats;
        $this->storageStats = $storageStats;
        $this->count = $count;
        $this->queryExecStats = $queryExecStats;
    }
}
