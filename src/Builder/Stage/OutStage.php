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
use MongoDB\Builder\Type\OutputStageInterface;
use stdClass;

/**
 * Writes the resulting documents of the aggregation pipeline to a collection. To use the $out stage, it must be the last stage in the pipeline.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/
 * @internal
 */
final class OutStage implements OutputStageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$out';
    public const PROPERTIES = ['coll' => 'coll', 'db' => 'db', 'timeseries' => 'timeseries'];

    /** @var string $coll The output collection name. */
    public readonly string $coll;

    /** @var Optional|string $db The output database name. If omitted, defaults to the current database. */
    public readonly Optional|string $db;

    /**
     * @var Optional|Document|Serializable|array|stdClass $timeseries Specifies the configuration to use when writing to a time series collection.
     * The timeField is required. All other fields are optional.
     *
     * New in MongoDB 7.0.3
     */
    public readonly Optional|Document|Serializable|stdClass|array $timeseries;

    /**
     * @param string $coll The output collection name.
     * @param Optional|string $db The output database name. If omitted, defaults to the current database.
     * @param Optional|Document|Serializable|array|stdClass $timeseries Specifies the configuration to use when writing to a time series collection.
     * The timeField is required. All other fields are optional.
     *
     * New in MongoDB 7.0.3
     */
    public function __construct(
        string $coll,
        Optional|string $db = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $timeseries = Optional::Undefined,
    ) {
        $this->coll = $coll;
        $this->db = $db;
        $this->timeseries = $timeseries;
    }
}
