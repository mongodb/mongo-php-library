<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Aggregation\AccumulatorInterface;
use MongoDB\Builder\Expression\ArrayFieldPath;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Query\QueryInterface;
use MongoDB\Builder\Stage\AddFieldsStage;
use MongoDB\Builder\Stage\BucketAutoStage;
use MongoDB\Builder\Stage\BucketStage;
use MongoDB\Builder\Stage\ChangeStreamSplitLargeEventStage;
use MongoDB\Builder\Stage\ChangeStreamStage;
use MongoDB\Builder\Stage\CollStatsStage;
use MongoDB\Builder\Stage\CountStage;
use MongoDB\Builder\Stage\CurrentOpStage;
use MongoDB\Builder\Stage\DensifyStage;
use MongoDB\Builder\Stage\DocumentsStage;
use MongoDB\Builder\Stage\FacetStage;
use MongoDB\Builder\Stage\FillStage;
use MongoDB\Builder\Stage\GeoNearStage;
use MongoDB\Builder\Stage\GraphLookupStage;
use MongoDB\Builder\Stage\GroupStage;
use MongoDB\Builder\Stage\IndexStatsStage;
use MongoDB\Builder\Stage\LimitStage;
use MongoDB\Builder\Stage\ListLocalSessionsStage;
use MongoDB\Builder\Stage\ListSampledQueriesStage;
use MongoDB\Builder\Stage\ListSearchIndexesStage;
use MongoDB\Builder\Stage\ListSessionsStage;
use MongoDB\Builder\Stage\LookupStage;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Builder\Stage\MergeStage;
use MongoDB\Builder\Stage\OutStage;
use MongoDB\Builder\Stage\PlanCacheStatsStage;
use MongoDB\Builder\Stage\ProjectStage;
use MongoDB\Builder\Stage\RedactStage;
use MongoDB\Builder\Stage\ReplaceRootStage;
use MongoDB\Builder\Stage\ReplaceWithStage;
use MongoDB\Builder\Stage\SampleStage;
use MongoDB\Builder\Stage\SearchMetaStage;
use MongoDB\Builder\Stage\SearchStage;
use MongoDB\Builder\Stage\SetStage;
use MongoDB\Builder\Stage\SetWindowFieldsStage;
use MongoDB\Builder\Stage\ShardedDataDistributionStage;
use MongoDB\Builder\Stage\SkipStage;
use MongoDB\Builder\Stage\SortByCountStage;
use MongoDB\Builder\Stage\SortStage;
use MongoDB\Builder\Stage\UnionWithStage;
use MongoDB\Builder\Stage\UnsetStage;
use MongoDB\Builder\Stage\UnwindStage;
use MongoDB\Model\BSONArray;

final class Stage
{
    /**
     * @param ExpressionInterface|mixed ...$expression Specify the name of each field to add and set its value to an aggregation expression or an empty object.
     */
    public static function addFields(mixed ...$expression): AddFieldsStage
    {
        return new AddFieldsStage(...$expression);
    }

    /**
     * @param ExpressionInterface|FieldPath|mixed|non-empty-string $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * Unless $bucket includes a default specification, each input document must resolve the groupBy field path or expression to a value that falls within one of the ranges specified by the boundaries.
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $boundaries An array of values based on the groupBy expression that specify the boundaries for each bucket. Each adjacent pair of values acts as the inclusive lower boundary and the exclusive upper boundary for the bucket. You must specify at least two boundaries.
     * The specified values must be in ascending order and all of the same type. The exception is if the values are of mixed numeric types, such as:
     * @param ExpressionInterface|Optional|mixed $default A literal that specifies the _id of an additional bucket that contains all documents whose groupBy expression result does not fall into a bucket specified by boundaries.
     * If unspecified, each input document must resolve the groupBy expression to a value within one of the bucket ranges specified by boundaries or the operation throws an error.
     * The default value must be less than the lowest boundaries value, or greater than or equal to the highest boundaries value.
     * The default value can be of a different type than the entries in boundaries.
     * @param Document|Optional|Serializable|array|object $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * If you do not specify an output document, the operation returns a count field containing the number of documents in each bucket.
     * If you specify an output document, only the fields specified in the document are returned; i.e. the count field is not returned unless it is explicitly included in the output document.
     */
    public static function bucket(
        mixed $groupBy,
        PackedArray|BSONArray|array $boundaries,
        mixed $default = Optional::Undefined,
        array|object $output = Optional::Undefined,
    ): BucketStage
    {
        return new BucketStage($groupBy, $boundaries, $default, $output);
    }

    /**
     * @param ExpressionInterface|mixed $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * @param Int64|int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped.
     * @param Document|Optional|Serializable|array|object $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     * @param Optional|non-empty-string $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public static function bucketAuto(
        mixed $groupBy,
        Int64|int $buckets,
        array|object $output = Optional::Undefined,
        Optional|string $granularity = Optional::Undefined,
    ): BucketAutoStage
    {
        return new BucketAutoStage($groupBy, $buckets, $output, $granularity);
    }

    /**
     * @param Optional|bool $allChangesForCluster A flag indicating whether the stream should report all changes that occur on the deployment, aside from those on internal databases or collections.
     * @param Optional|non-empty-string $fullDocument Specifies whether change notifications include a copy of the full document when modified by update operations.
     * @param Optional|non-empty-string $fullDocumentBeforeChange Valid values are "off", "whenAvailable", or "required". If set to "off", the "fullDocumentBeforeChange" field of the output document is always omitted. If set to "whenAvailable", the "fullDocumentBeforeChange" field will be populated with the pre-image of the document modified by the current change event if such a pre-image is available, and will be omitted otherwise. If set to "required", then the "fullDocumentBeforeChange" field is always populated and an exception is thrown if the pre-image is not              available.
     * @param Int64|Optional|int $resumeAfter Specifies a resume token as the logical starting point for the change stream. Cannot be used with startAfter or startAtOperationTime fields.
     * @param Optional|bool $showExpandedEvents Specifies whether to include additional change events, such as such as DDL and index operations.
     * New in version 6.0.
     * @param Document|Optional|Serializable|array|object $startAfter Specifies a resume token as the logical starting point for the change stream. Cannot be used with resumeAfter or startAtOperationTime fields.
     * @param Optional|int $startAtOperationTime Specifies a time as the logical starting point for the change stream. Cannot be used with resumeAfter or startAfter fields.
     */
    public static function changeStream(
        Optional|bool $allChangesForCluster = Optional::Undefined,
        Optional|string $fullDocument = Optional::Undefined,
        Optional|string $fullDocumentBeforeChange = Optional::Undefined,
        Int64|Optional|int $resumeAfter = Optional::Undefined,
        Optional|bool $showExpandedEvents = Optional::Undefined,
        array|object $startAfter = Optional::Undefined,
        Optional|int $startAtOperationTime = Optional::Undefined,
    ): ChangeStreamStage
    {
        return new ChangeStreamStage($allChangesForCluster, $fullDocument, $fullDocumentBeforeChange, $resumeAfter, $showExpandedEvents, $startAfter, $startAtOperationTime);
    }

    public static function changeStreamSplitLargeEvent(): ChangeStreamSplitLargeEventStage
    {
        return new ChangeStreamSplitLargeEventStage();
    }

    /**
     * @param Document|Serializable|array|object $config
     */
    public static function collStats(array|object $config): CollStatsStage
    {
        return new CollStatsStage($config);
    }

    /**
     * @param non-empty-string $field
     */
    public static function count(string $field): CountStage
    {
        return new CountStage($field);
    }

    public static function currentOp(): CurrentOpStage
    {
        return new CurrentOpStage();
    }

    /**
     * @param FieldPath|non-empty-string $field The field to densify. The values of the specified field must either be all numeric values or all dates.
     * Documents that do not contain the specified field continue through the pipeline unmodified.
     * To specify a <field> in an embedded document or in an array, use dot notation.
     * @param array|object $range Specification for range based densification.
     * @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed> $partitionByFields The field(s) that will be used as the partition keys.
     */
    public static function densify(
        FieldPath|string $field,
        array|object $range,
        PackedArray|Optional|BSONArray|array $partitionByFields = Optional::Undefined,
    ): DensifyStage
    {
        return new DensifyStage($field, $range, $partitionByFields);
    }

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $documents $documents accepts any valid expression that resolves to an array of objects. This includes:
     * - system variables, such as $$NOW or $$SEARCH_META
     * - $let expressions
     * - variables in scope from $lookup expressions
     * Expressions that do not resolve to a current document, like $myField or $$ROOT, will result in an error.
     */
    public static function documents(PackedArray|ResolvesToArray|BSONArray|array $documents): DocumentsStage
    {
        return new DocumentsStage($documents);
    }

    /**
     * @param Pipeline|array ...$facet
     */
    public static function facet(Pipeline|array ...$facet): FacetStage
    {
        return new FacetStage(...$facet);
    }

    /**
     * @param Document|Serializable|array|object $output Specifies an object containing each field for which to fill missing values. You can specify multiple fields in the output object.
     * The object name is the name of the field to fill. The object value specifies how the field is filled.
     * @param Document|Optional|Serializable|array|non-empty-string|object $partitionBy Specifies an expression to group the documents. In the $fill stage, a group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     * @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed> $partitionByFields Specifies an array of fields as the compound key to group the documents. In the $fill stage, each group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     * @param Optional|array|object $sortBy Specifies the field or fields to sort the documents within each partition. Uses the same syntax as the $sort stage.
     */
    public static function fill(
        array|object $output,
        array|object|string $partitionBy = Optional::Undefined,
        PackedArray|Optional|BSONArray|array $partitionByFields = Optional::Undefined,
        array|object $sortBy = Optional::Undefined,
    ): FillStage
    {
        return new FillStage($output, $partitionBy, $partitionByFields, $sortBy);
    }

    /**
     * @param non-empty-string $distanceField The output field that contains the calculated distance. To specify a field within an embedded document, use dot notation.
     * @param array|object $near The point for which to find the closest documents.
     * @param Decimal128|Int64|Optional|float|int $distanceMultiplier The factor to multiply all distances returned by the query. For example, use the distanceMultiplier to convert radians, as returned by a spherical query, to kilometers by multiplying by the radius of the Earth.
     * @param Optional|non-empty-string $includeLocs This specifies the output field that identifies the location used to calculate the distance. This option is useful when a location field contains multiple locations. To specify a field within an embedded document, use dot notation.
     * @param Optional|non-empty-string $key Specify the geospatial indexed field to use when calculating the distance.
     * @param Decimal128|Int64|Optional|float|int $maxDistance The maximum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall within the specified distance from the center point.
     * Specify the distance in meters if the specified point is GeoJSON and in radians if the specified point is legacy coordinate pairs.
     * @param Decimal128|Int64|Optional|float|int $minDistance The minimum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall outside the specified distance from the center point.
     * Specify the distance in meters for GeoJSON data and in radians for legacy coordinate pairs.
     * @param Optional|QueryInterface|array|object $query imits the results to the documents that match the query. The query syntax is the usual MongoDB read operation query syntax.
     * You cannot specify a $near predicate in the query field of the $geoNear stage.
     * @param Optional|bool $spherical Determines how MongoDB calculates the distance between two points:
     * - When true, MongoDB uses $nearSphere semantics and calculates distances using spherical geometry.
     * - When false, MongoDB uses $near semantics: spherical geometry for 2dsphere indexes and planar geometry for 2d indexes.
     * Default: false.
     */
    public static function geoNear(
        string $distanceField,
        array|object $near,
        Decimal128|Int64|Optional|float|int $distanceMultiplier = Optional::Undefined,
        Optional|string $includeLocs = Optional::Undefined,
        Optional|string $key = Optional::Undefined,
        Decimal128|Int64|Optional|float|int $maxDistance = Optional::Undefined,
        Decimal128|Int64|Optional|float|int $minDistance = Optional::Undefined,
        array|object $query = Optional::Undefined,
        Optional|bool $spherical = Optional::Undefined,
    ): GeoNearStage
    {
        return new GeoNearStage($distanceField, $near, $distanceMultiplier, $includeLocs, $key, $maxDistance, $minDistance, $query, $spherical);
    }

    /**
     * @param non-empty-string $from Target collection for the $graphLookup operation to search, recursively matching the connectFromField to the connectToField. The from collection must be in the same database as any other collections used in the operation.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     * @param BSONArray|ExpressionInterface|PackedArray|list<ExpressionInterface|mixed>|mixed $startWith Expression that specifies the value of the connectFromField with which to start the recursive search. Optionally, startWith may be array of values, each of which is individually followed through the traversal process.
     * @param non-empty-string $connectFromField Field name whose value $graphLookup uses to recursively match against the connectToField of other documents in the collection. If the value is an array, each element is individually followed through the traversal process.
     * @param non-empty-string $connectToField Field name in other documents against which to match the value of the field specified by the connectFromField parameter.
     * @param non-empty-string $as Name of the array field added to each output document. Contains the documents traversed in the $graphLookup stage to reach the document.
     * @param Int64|Optional|int $maxDepth Non-negative integral number specifying the maximum recursion depth.
     * @param Optional|non-empty-string $depthField Name of the field to add to each traversed document in the search path. The value of this field is the recursion depth for the document, represented as a NumberLong. Recursion depth value starts at zero, so the first lookup corresponds to zero depth.
     * @param Optional|QueryInterface|array|object $restrictSearchWithMatch A document specifying additional conditions for the recursive search. The syntax is identical to query filter syntax.
     */
    public static function graphLookup(
        string $from,
        mixed $startWith,
        string $connectFromField,
        string $connectToField,
        string $as,
        Int64|Optional|int $maxDepth = Optional::Undefined,
        Optional|string $depthField = Optional::Undefined,
        array|object $restrictSearchWithMatch = Optional::Undefined,
    ): GraphLookupStage
    {
        return new GraphLookupStage($from, $startWith, $connectFromField, $connectToField, $as, $maxDepth, $depthField, $restrictSearchWithMatch);
    }

    /**
     * @param ExpressionInterface|mixed $_id The _id expression specifies the group key. If you specify an _id value of null, or any other constant value, the $group stage returns a single document that aggregates values across all of the input documents.
     * @param AccumulatorInterface ...$field Computed using the accumulator operators.
     */
    public static function group(mixed $_id, AccumulatorInterface ...$field): GroupStage
    {
        return new GroupStage($_id, ...$field);
    }

    public static function indexStats(): IndexStatsStage
    {
        return new IndexStatsStage();
    }

    /**
     * @param Int64|int $limit
     */
    public static function limit(Int64|int $limit): LimitStage
    {
        return new LimitStage($limit);
    }

    /**
     * @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed> $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users.
     * @param Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster.
     */
    public static function listLocalSessions(
        PackedArray|Optional|BSONArray|array $users = Optional::Undefined,
        Optional|bool $allUsers = Optional::Undefined,
    ): ListLocalSessionsStage
    {
        return new ListLocalSessionsStage($users, $allUsers);
    }

    /**
     * @param Optional|non-empty-string $namespace
     */
    public static function listSampledQueries(
        Optional|string $namespace = Optional::Undefined,
    ): ListSampledQueriesStage
    {
        return new ListSampledQueriesStage($namespace);
    }

    /**
     * @param Optional|non-empty-string $id The id of the index to return information about.
     * @param Optional|non-empty-string $name The name of the index to return information about.
     */
    public static function listSearchIndexes(
        Optional|string $id = Optional::Undefined,
        Optional|string $name = Optional::Undefined,
    ): ListSearchIndexesStage
    {
        return new ListSearchIndexesStage($id, $name);
    }

    /**
     * @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed> $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users.
     * @param Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster.
     */
    public static function listSessions(
        PackedArray|Optional|BSONArray|array $users = Optional::Undefined,
        Optional|bool $allUsers = Optional::Undefined,
    ): ListSessionsStage
    {
        return new ListSessionsStage($users, $allUsers);
    }

    /**
     * @param non-empty-string $as Specifies the name of the new array field to add to the input documents. The new array field contains the matching documents from the from collection. If the specified name already exists in the input document, the existing field is overwritten.
     * @param Optional|non-empty-string $from Specifies the collection in the same database to perform the join with.
     * from is optional, you can use a $documents stage in a $lookup stage instead. For an example, see Use a $documents Stage in a $lookup Stage.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     * @param Optional|non-empty-string $localField Specifies the field from the documents input to the $lookup stage. $lookup performs an equality match on the localField to the foreignField from the documents of the from collection. If an input document does not contain the localField, the $lookup treats the field as having a value of null for matching purposes.
     * @param Optional|non-empty-string $foreignField Specifies the field from the documents in the from collection. $lookup performs an equality match on the foreignField to the localField from the input documents. If a document in the from collection does not contain the foreignField, the $lookup treats the value as null for matching purposes.
     * @param Document|Optional|Serializable|array|object $let Specifies variables to use in the pipeline stages. Use the variable expressions to access the fields from the joined collection's documents that are input to the pipeline.
     * @param Optional|Pipeline|array $pipeline Specifies the pipeline to run on the joined collection. The pipeline determines the resulting documents from the joined collection. To return all documents, specify an empty pipeline [].
     * The pipeline cannot include the $out stage or the $mergestage. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     * The pipeline cannot directly access the joined document fields. Instead, define variables for the joined document fields using the let option and then reference the variables in the pipeline stages.
     */
    public static function lookup(
        string $as,
        Optional|string $from = Optional::Undefined,
        Optional|string $localField = Optional::Undefined,
        Optional|string $foreignField = Optional::Undefined,
        array|object $let = Optional::Undefined,
        Optional|Pipeline|array $pipeline = Optional::Undefined,
    ): LookupStage
    {
        return new LookupStage($as, $from, $localField, $foreignField, $let, $pipeline);
    }

    /**
     * @param QueryInterface|array|object ...$query
     */
    public static function match(array|object ...$query): MatchStage
    {
        return new MatchStage(...$query);
    }

    /**
     * @param array|non-empty-string|object $into The output collection.
     * @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed>|non-empty-string $on Field or fields that act as a unique identifier for a document. The identifier determines if a results document matches an existing document in the output collection.
     * @param Document|Optional|Serializable|array|object $let Specifies variables for use in the whenMatched pipeline.
     * @param Optional|non-empty-string $whenMatched The behavior of $merge if a result document and an existing document in the collection have the same value for the specified on field(s).
     * @param Optional|non-empty-string $whenNotMatched The behavior of $merge if a result document does not match an existing document in the out collection.
     */
    public static function merge(
        array|object|string $into,
        PackedArray|Optional|BSONArray|array|string $on = Optional::Undefined,
        array|object $let = Optional::Undefined,
        Optional|string $whenMatched = Optional::Undefined,
        Optional|string $whenNotMatched = Optional::Undefined,
    ): MergeStage
    {
        return new MergeStage($into, $on, $let, $whenMatched, $whenNotMatched);
    }

    /**
     * @param non-empty-string $db Target collection name to write documents from $out to.
     * @param non-empty-string $coll Target database name to write documents from $out to.
     * @param Document|Serializable|array|object $timeseries If set, the aggregation stage will use these options to create or replace a time-series collection in the given namespace.
     */
    public static function out(string $db, string $coll, array|object $timeseries): OutStage
    {
        return new OutStage($db, $coll, $timeseries);
    }

    public static function planCacheStats(): PlanCacheStatsStage
    {
        return new PlanCacheStatsStage();
    }

    /**
     * @param ExpressionInterface|Int64|bool|int|mixed ...$specification
     */
    public static function project(mixed ...$specification): ProjectStage
    {
        return new ProjectStage(...$specification);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function redact(mixed $expression): RedactStage
    {
        return new RedactStage($expression);
    }

    /**
     * @param Document|ResolvesToObject|Serializable|array|object $newRoot
     */
    public static function replaceRoot(array|object $newRoot): ReplaceRootStage
    {
        return new ReplaceRootStage($newRoot);
    }

    /**
     * @param Document|ResolvesToObject|Serializable|array|object $expression
     */
    public static function replaceWith(array|object $expression): ReplaceWithStage
    {
        return new ReplaceWithStage($expression);
    }

    /**
     * @param Int64|int $size The number of documents to randomly select.
     */
    public static function sample(Int64|int $size): SampleStage
    {
        return new SampleStage($size);
    }

    /**
     * @param Document|Serializable|array|object $search
     */
    public static function search(array|object $search): SearchStage
    {
        return new SearchStage($search);
    }

    /**
     * @param Document|Serializable|array|object $meta
     */
    public static function searchMeta(array|object $meta): SearchMetaStage
    {
        return new SearchMetaStage($meta);
    }

    /**
     * @param ExpressionInterface|mixed ...$field
     */
    public static function set(mixed ...$field): SetStage
    {
        return new SetStage(...$field);
    }

    /**
     * @param ExpressionInterface|mixed $partitionBy Specifies an expression to group the documents. In the $setWindowFields stage, the group of documents is known as a partition. Default is one partition for the entire collection.
     * @param array|object $sortBy Specifies the field(s) to sort the documents by in the partition. Uses the same syntax as the $sort stage. Default is no sorting.
     * @param Document|Serializable|array|object $output Specifies the field(s) to append to the documents in the output returned by the $setWindowFields stage. Each field is set to the result returned by the window operator.
     * A field can contain dots to specify embedded document fields and array fields. The semantics for the embedded document dotted notation in the $setWindowFields stage are the same as the $addFields and $set stages.
     * @param Optional|array|object $window Specifies the window boundaries and parameters. Window boundaries are inclusive. Default is an unbounded window, which includes all documents in the partition.
     */
    public static function setWindowFields(
        mixed $partitionBy,
        array|object $sortBy,
        array|object $output,
        array|object $window = Optional::Undefined,
    ): SetWindowFieldsStage
    {
        return new SetWindowFieldsStage($partitionBy, $sortBy, $output, $window);
    }

    public static function shardedDataDistribution(): ShardedDataDistributionStage
    {
        return new ShardedDataDistributionStage();
    }

    /**
     * @param Int64|int $skip
     */
    public static function skip(Int64|int $skip): SkipStage
    {
        return new SkipStage($skip);
    }

    /**
     * @param array|object $sort
     */
    public static function sort(array|object $sort): SortStage
    {
        return new SortStage($sort);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function sortByCount(mixed $expression): SortByCountStage
    {
        return new SortByCountStage($expression);
    }

    /**
     * @param non-empty-string $coll The collection or view whose pipeline results you wish to include in the result set.
     * @param Optional|Pipeline|array $pipeline An aggregation pipeline to apply to the specified coll.
     * The pipeline cannot include the $out and $merge stages. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     */
    public static function unionWith(
        string $coll,
        Optional|Pipeline|array $pipeline = Optional::Undefined,
    ): UnionWithStage
    {
        return new UnionWithStage($coll, $pipeline);
    }

    /**
     * @param FieldPath|non-empty-string ...$field
     */
    public static function unset(FieldPath|string ...$field): UnsetStage
    {
        return new UnsetStage(...$field);
    }

    /**
     * @param ArrayFieldPath|non-empty-string $field
     */
    public static function unwind(ArrayFieldPath|string $field): UnwindStage
    {
        return new UnwindStage($field);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
