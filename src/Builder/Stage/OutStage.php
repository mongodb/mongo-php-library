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
 * Writes the resulting documents of the aggregation pipeline to a collection. To use the $out stage, it must be the last stage in the pipeline.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/
 */
class OutStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var non-empty-string $db Target collection name to write documents from $out to. */
    public readonly string $db;

    /** @var non-empty-string $coll Target database name to write documents from $out to. */
    public readonly string $coll;

    /** @var Optional|Document|Serializable|array|stdClass $timeseries If set, the aggregation stage will use these options to create or replace a time-series collection in the given namespace. */
    public readonly Optional|Document|Serializable|stdClass|array $timeseries;

    /**
     * @param non-empty-string $db Target collection name to write documents from $out to.
     * @param non-empty-string $coll Target database name to write documents from $out to.
     * @param Optional|Document|Serializable|array|stdClass $timeseries If set, the aggregation stage will use these options to create or replace a time-series collection in the given namespace.
     */
    public function __construct(
        string $db,
        string $coll,
        Optional|Document|Serializable|stdClass|array $timeseries = Optional::Undefined,
    ) {
        $this->db = $db;
        $this->coll = $coll;
        $this->timeseries = $timeseries;
    }

    public function getOperator(): string
    {
        return '$out';
    }
}
