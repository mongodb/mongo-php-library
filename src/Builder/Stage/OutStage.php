<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use stdClass;

/**
 * Writes the resulting documents of the aggregation pipeline to a collection. To use the $out stage, it must be the last stage in the pipeline.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/
 */
class OutStage implements StageInterface
{
    public const NAME = '$out';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param non-empty-string $db Target collection name to write documents from $out to. */
    public string $db;

    /** @param non-empty-string $coll Target database name to write documents from $out to. */
    public string $coll;

    /** @param Document|Optional|Serializable|array|stdClass $timeseries If set, the aggregation stage will use these options to create or replace a time-series collection in the given namespace. */
    public Document|Serializable|Optional|stdClass|array $timeseries;

    /**
     * @param non-empty-string $db Target collection name to write documents from $out to.
     * @param non-empty-string $coll Target database name to write documents from $out to.
     * @param Document|Optional|Serializable|array|stdClass $timeseries If set, the aggregation stage will use these options to create or replace a time-series collection in the given namespace.
     */
    public function __construct(
        string $db,
        string $coll,
        Document|Serializable|Optional|stdClass|array $timeseries = Optional::Undefined,
    ) {
        $this->db = $db;
        $this->coll = $coll;
        $this->timeseries = $timeseries;
    }
}
