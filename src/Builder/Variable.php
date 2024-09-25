<?php

declare(strict_types=1);

namespace MongoDB\Builder;

use MongoDB\Builder\Expression\ResolvesToAny;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Expression\ResolvesToTimestamp;
use MongoDB\Builder\Type\ExpressionInterface;

/**
 * Enum for system variables that can be used in aggregation expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/aggregation-variables/
 */
final class Variable
{
    /**
     * A variable that returns the current timestamp value.
     * CLUSTER_TIME is only available on replica sets and sharded clusters.
     * CLUSTER_TIME returns the same value for all members of the deployment and remains the same throughout all stages
     * of the pipeline.
     *
     * New in MongoDB 4.2.
     */
    public static function clusterTime(): ResolvesToTimestamp
    {
        return new Expression\Variable('CLUSTER_TIME');
    }

    /**
     * References the start of the field path being processed in the aggregation pipeline stage.
     * Unless documented otherwise, all stages start with CURRENT the same as ROOT.
     * CURRENT is modifiable. However, since $<field> is equivalent to $$CURRENT.<field>, rebinding
     * CURRENT changes the meaning of $ accesses.
     */
    public static function current(string $fieldPath = ''): ResolvesToAny
    {
        return new Expression\Variable('CURRENT' . ($fieldPath ? '.' . $fieldPath : ''));
    }

    /**
     * One of the allowed results of a $redact expression.
     *
     * $redact returns the fields at the current document level, excluding embedded documents. To include embedded
     * documents and embedded documents within arrays, apply the $cond expression to the embedded documents to determine
     * access for these embedded documents.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/#mongodb-pipeline-pipe.-redact
     */
    public static function descend(): ExpressionInterface
    {
        return new Expression\Variable('DESCEND');
    }

    /**
     * One of the allowed results of a $redact expression.
     *
     * $redact returns or keeps all fields at this current document/embedded document level, without further inspection
     * of the fields at this level. This applies even if the included field contains embedded documents that may have
     * different access levels.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/#mongodb-pipeline-pipe.-redact
     */
    public static function keep(): ExpressionInterface
    {
        return new Expression\Variable('KEEP');
    }

    /**
     * A variable that returns the current datetime value.
     * NOW returns the same value for all members of the deployment and remains the same throughout all stages of the
     * aggregation pipeline.
     *
     * New in MongoDB 4.2.
     */
    public static function now(): ResolvesToDate
    {
        return new Expression\Variable('NOW');
    }

    /**
     * One of the allowed results of a $redact expression.
     *
     * $redact excludes all fields at this current document/embedded document level, without further inspection of any
     * of the excluded fields. This applies even if the excluded field contains embedded documents that may have
     * different access levels.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/#mongodb-pipeline-pipe.-redact
     */
    public static function prune(): ExpressionInterface
    {
        return new Expression\Variable('PRUNE');
    }

    /**
     * A variable which evaluates to the missing value. Allows for the conditional exclusion of fields. In a $project,
     * a field set to the variable REMOVE is excluded from the output.
     * Can be used with $cond operator for conditionally exclude fields.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#std-label-remove-example
     */
    public static function remove(): ResolvesToAny
    {
        return new Expression\Variable('REMOVE');
    }

    /**
     * References the root document, i.e. the top-level document, currently being processed in the aggregation pipeline
     * stage.
     */
    public static function root(): ResolvesToObject
    {
        return new Expression\Variable('ROOT');
    }

    /**
     * A variable that stores the metadata results of an Atlas Search query. In all supported aggregation pipeline
     * stages, a field set to the variable $$SEARCH_META returns the metadata results for the query.
     * For an example of its usage, see Atlas Search facet and count.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/query-syntax/#metadata-result-types
     */
    public static function searchMeta(): ResolvesToObject
    {
        return new Expression\Variable('SEARCH_META');
    }

    /**
     * Returns the roles assigned to the current user.
     * For use cases that include USER_ROLES, see the find, aggregation, view, updateOne, updateMany, and findAndModify
     * examples.
     *
     * New in MongoDB 7.0.
     */
    public static function userRoles(): ResolvesToArray
    {
        return new Expression\Variable('USER_ROLES');
    }

    /**
     * User-defined variable that can be used to store any BSON type.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/let/
     */
    public static function variable(string $name): Expression\Variable
    {
        return new Expression\Variable($name);
    }

    private function __construct()
    {
        // This class cannot be instantiated
    }
}
