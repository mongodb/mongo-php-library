<?php

declare(strict_types=1);

namespace MongoDB\Builder;

use DateTimeInterface;
use InvalidArgumentException;
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Builder\Stage\OutStage;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

use function get_debug_type;
use function is_array;
use function is_string;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

final class Stage
{
    use Stage\FactoryTrait {
        match as private generatedMatch;
    }

    /**
     * Filters the document stream to allow only matching documents to pass unmodified into the next pipeline stage. $match uses standard MongoDB queries. For each input document, outputs either one document (a match) or zero documents (no match).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/
     *
     * @param DateTimeInterface|QueryInterface|FieldQueryInterface|Type|stdClass|array<array-key, mixed>|bool|float|int|string|null ...$queries The query predicates to match
     */
    public static function match(DateTimeInterface|QueryInterface|FieldQueryInterface|Type|stdClass|array|bool|float|int|string|null ...$queries): MatchStage
    {
        // Override the generated method to allow variadic arguments
        return self::generatedMatch($queries);
    }

    /**
     * Writes the resulting documents of the aggregation pipeline to a collection. To use the $out stage, it must be the last stage in the pipeline.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/
     * @param Document|Serializable|array|stdClass|string   $coll       The output collection name. Passing a non-string value is deprecated since 2.4.
     * @param Optional|string                               $db         The output database name. If omitted, defaults to the current database.
     * @param Optional|Document|Serializable|array|stdClass $timeseries Specifies the configuration to use when writing to a time series collection.
     * The timeField is required. All other fields are optional.
     *
     * New in MongoDB 7.0.3
     */
    public static function out(
        Document|Serializable|stdClass|array|string $coll,
        Optional|string $db = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $timeseries = Optional::Undefined,
    ): OutStage {
        if (! is_string($coll)) {
            trigger_error(sprintf('Since mongodb/mongodb 2.4: %s::%s() first argument must be the collection name. Passing "%s" is deprecated', self::class, __FUNCTION__, get_debug_type($coll)), E_USER_DEPRECATED);

            if ($db !== Optional::Undefined || $timeseries !== Optional::Undefined) {
                throw new InvalidArgumentException('When passing a non-string first argument, the $db and $timeseries arguments must not be passed.');
            }

            if ($coll instanceof Serializable) {
                $coll = (array) $coll->bsonSerialize();
            } elseif ($coll instanceof stdClass) {
                $coll = (array) $coll;
            }

            if (! is_array($coll) || ! isset($coll['coll']) || ! is_string($coll['coll'])) {
                throw new InvalidArgumentException('When passing a non-string first argument, it must be an array or object with a "coll" string field.');
            }

            if (isset($coll['db'])) {
                $db = (string) $coll['db'];
            }

            $coll = $coll['coll'];
        }

        return new OutStage($coll, $db, $timeseries);
    }

    private function __construct()
    {
        // This class cannot be instantiated
    }
}
