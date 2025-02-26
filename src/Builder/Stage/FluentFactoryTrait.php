<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use DateTimeInterface;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\Type;
use MongoDB\Builder\Expression\ArrayFieldPath;
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\SearchOperatorInterface;
use MongoDB\Builder\Type\Sort;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Model\BSONArray;
use stdClass;

trait FluentFactoryTrait
{
    /** @var list<StageInterface|array<string,mixed>|stdClass> */
    public array $pipeline = [];

    public function getPipeline(): Pipeline
    {
        return new Pipeline(...$this->pipeline);
    }

    /**
     * Filters the document stream to allow only matching documents to pass unmodified into the next pipeline stage. $match uses standard MongoDB queries. For each input document, outputs either one document (a match) or zero documents (no match).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/
     *
     * @param DateTimeInterface|QueryInterface|FieldQueryInterface|Type|stdClass|array<array-key, mixed>|bool|float|int|string|null ...$queries The query predicates to match
     */
    public function match(
        DateTimeInterface|QueryInterface|FieldQueryInterface|Type|stdClass|array|string|int|float|bool|null ...$queries,
    ): static {
        $this->pipeline[] = Stage::match(...$queries);

        return $this;
    }

    /**
     * Adds new fields to documents. Outputs documents that contain all existing fields from the input documents and newly added fields.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$expression Specify the name of each field to add and set its value to an aggregation expression or an empty object.
     */
    public function addFields(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null ...$expression,
    ): static {
        $this->pipeline[] = Stage::addFields(...$expression);

        return $this;
    }

    /**
     * Categorizes incoming documents into groups, called buckets, based on a specified expression and bucket boundaries.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucket/
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * Unless $bucket includes a default specification, each input document must resolve the groupBy field path or expression to a value that falls within one of the ranges specified by the boundaries.
     * @param BSONArray|PackedArray|array $boundaries An array of values based on the groupBy expression that specify the boundaries for each bucket. Each adjacent pair of values acts as the inclusive lower boundary and the exclusive upper boundary for the bucket. You must specify at least two boundaries.
     * The specified values must be in ascending order and all of the same type. The exception is if the values are of mixed numeric types, such as:
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $default A literal that specifies the _id of an additional bucket that contains all documents whose groupBy expression result does not fall into a bucket specified by boundaries.
     * If unspecified, each input document must resolve the groupBy expression to a value within one of the bucket ranges specified by boundaries or the operation throws an error.
     * The default value must be less than the lowest boundaries value, or greater than or equal to the highest boundaries value.
     * The default value can be of a different type than the entries in boundaries.
     * @param Optional|Document|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * If you do not specify an output document, the operation returns a count field containing the number of documents in each bucket.
     * If you specify an output document, only the fields specified in the document are returned; i.e. the count field is not returned unless it is explicitly included in the output document.
     */
    public function bucket(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null $groupBy,
        PackedArray|BSONArray|array $boundaries,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null $default = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $output = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::bucket($groupBy, $boundaries, $default, $output);

        return $this;
    }

    /**
     * Categorizes incoming documents into a specific number of groups, called buckets, based on a specified expression. Bucket boundaries are automatically determined in an attempt to evenly distribute the documents into the specified number of buckets.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucketAuto/
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $groupBy An expression to group documents by. To specify a field path, prefix the field name with a dollar sign $ and enclose it in quotes.
     * @param int $buckets A positive 32-bit integer that specifies the number of buckets into which input documents are grouped.
     * @param Optional|Document|Serializable|array|stdClass $output A document that specifies the fields to include in the output documents in addition to the _id field. To specify the field to include, you must use accumulator expressions.
     * The default count field is not included in the output document when output is specified. Explicitly specify the count expression as part of the output document to include it.
     * @param Optional|string $granularity A string that specifies the preferred number series to use to ensure that the calculated boundary edges end on preferred round numbers or their powers of 10.
     * Available only if the all groupBy values are numeric and none of them are NaN.
     */
    public function bucketAuto(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null $groupBy,
        int $buckets,
        Optional|Document|Serializable|stdClass|array $output = Optional::Undefined,
        Optional|string $granularity = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::bucketAuto($groupBy, $buckets, $output, $granularity);

        return $this;
    }

    /**
     * Returns a Change Stream cursor for the collection or database. This stage can only occur once in an aggregation pipeline and it must occur as the first stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStream/
     * @param Optional|bool $allChangesForCluster A flag indicating whether the stream should report all changes that occur on the deployment, aside from those on internal databases or collections.
     * @param Optional|string $fullDocument Specifies whether change notifications include a copy of the full document when modified by update operations.
     * @param Optional|string $fullDocumentBeforeChange Valid values are "off", "whenAvailable", or "required". If set to "off", the "fullDocumentBeforeChange" field of the output document is always omitted. If set to "whenAvailable", the "fullDocumentBeforeChange" field will be populated with the pre-image of the document modified by the current change event if such a pre-image is available, and will be omitted otherwise. If set to "required", then the "fullDocumentBeforeChange" field is always populated and an exception is thrown if the pre-image is not              available.
     * @param Optional|int $resumeAfter Specifies a resume token as the logical starting point for the change stream. Cannot be used with startAfter or startAtOperationTime fields.
     * @param Optional|bool $showExpandedEvents Specifies whether to include additional change events, such as such as DDL and index operations.
     * New in MongoDB 6.0.
     * @param Optional|Document|Serializable|array|stdClass $startAfter Specifies a resume token as the logical starting point for the change stream. Cannot be used with resumeAfter or startAtOperationTime fields.
     * @param Optional|Timestamp|int $startAtOperationTime Specifies a time as the logical starting point for the change stream. Cannot be used with resumeAfter or startAfter fields.
     */
    public function changeStream(
        Optional|bool $allChangesForCluster = Optional::Undefined,
        Optional|string $fullDocument = Optional::Undefined,
        Optional|string $fullDocumentBeforeChange = Optional::Undefined,
        Optional|int $resumeAfter = Optional::Undefined,
        Optional|bool $showExpandedEvents = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $startAfter = Optional::Undefined,
        Optional|Timestamp|int $startAtOperationTime = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::changeStream($allChangesForCluster, $fullDocument, $fullDocumentBeforeChange, $resumeAfter, $showExpandedEvents, $startAfter, $startAtOperationTime);

        return $this;
    }

    /**
     * Splits large change stream events that exceed 16 MB into smaller fragments returned in a change stream cursor.
     * You can only use $changeStreamSplitLargeEvent in a $changeStream pipeline and it must be the final stage in the pipeline.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStreamSplitLargeEvent/
     */
    public function changeStreamSplitLargeEvent(): static
    {
        $this->pipeline[] = Stage::changeStreamSplitLargeEvent();

        return $this;
    }

    /**
     * Returns statistics regarding a collection or view.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/
     * @param Optional|Document|Serializable|array|stdClass $latencyStats
     * @param Optional|Document|Serializable|array|stdClass $storageStats
     * @param Optional|Document|Serializable|array|stdClass $count
     * @param Optional|Document|Serializable|array|stdClass $queryExecStats
     */
    public function collStats(
        Optional|Document|Serializable|stdClass|array $latencyStats = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $storageStats = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $count = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $queryExecStats = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::collStats($latencyStats, $storageStats, $count, $queryExecStats);

        return $this;
    }

    /**
     * Returns a count of the number of documents at this stage of the aggregation pipeline.
     * Distinct from the $count aggregation accumulator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count/
     * @param string $field Name of the output field which has the count as its value. It must be a non-empty string, must not start with $ and must not contain the . character.
     */
    public function count(string $field): static
    {
        $this->pipeline[] = Stage::count($field);

        return $this;
    }

    /**
     * Returns information on active and/or dormant operations for the MongoDB deployment. To run, use the db.aggregate() method.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/currentOp/
     * @param Optional|bool $allUsers
     * @param Optional|bool $idleConnections
     * @param Optional|bool $idleCursors
     * @param Optional|bool $idleSessions
     * @param Optional|bool $localOps
     */
    public function currentOp(
        Optional|bool $allUsers = Optional::Undefined,
        Optional|bool $idleConnections = Optional::Undefined,
        Optional|bool $idleCursors = Optional::Undefined,
        Optional|bool $idleSessions = Optional::Undefined,
        Optional|bool $localOps = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::currentOp($allUsers, $idleConnections, $idleCursors, $idleSessions, $localOps);

        return $this;
    }

    /**
     * Creates new documents in a sequence of documents where certain values in a field are missing.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/densify/
     * @param string $field The field to densify. The values of the specified field must either be all numeric values or all dates.
     * Documents that do not contain the specified field continue through the pipeline unmodified.
     * To specify a <field> in an embedded document or in an array, use dot notation.
     * @param Document|Serializable|array|stdClass $range Specification for range based densification.
     * @param Optional|BSONArray|PackedArray|array $partitionByFields The field(s) that will be used as the partition keys.
     */
    public function densify(
        string $field,
        Document|Serializable|stdClass|array $range,
        Optional|PackedArray|BSONArray|array $partitionByFields = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::densify($field, $range, $partitionByFields);

        return $this;
    }

    /**
     * Returns literal documents from input values.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documents/
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $documents $documents accepts any valid expression that resolves to an array of objects. This includes:
     * - system variables, such as $$NOW or $$SEARCH_META
     * - $let expressions
     * - variables in scope from $lookup expressions
     * Expressions that do not resolve to a current document, like $myField or $$ROOT, will result in an error.
     */
    public function documents(PackedArray|ResolvesToArray|BSONArray|array|string $documents): static
    {
        $this->pipeline[] = Stage::documents($documents);

        return $this;
    }

    /**
     * Processes multiple aggregation pipelines within a single stage on the same set of input documents. Enables the creation of multi-faceted aggregations capable of characterizing data across multiple dimensions, or facets, in a single stage.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/facet/
     * @param BSONArray|PackedArray|Pipeline|array ...$facet
     */
    public function facet(PackedArray|Pipeline|BSONArray|array ...$facet): static
    {
        $this->pipeline[] = Stage::facet(...$facet);

        return $this;
    }

    /**
     * Populates null and missing field values within documents.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/
     * @param Document|Serializable|array|stdClass $output Specifies an object containing each field for which to fill missing values. You can specify multiple fields in the output object.
     * The object name is the name of the field to fill. The object value specifies how the field is filled.
     * @param Optional|Document|Serializable|array|stdClass|string $partitionBy Specifies an expression to group the documents. In the $fill stage, a group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     * @param Optional|BSONArray|PackedArray|array $partitionByFields Specifies an array of fields as the compound key to group the documents. In the $fill stage, each group of documents is known as a partition.
     * If you omit partitionBy and partitionByFields, $fill uses one partition for the entire collection.
     * partitionBy and partitionByFields are mutually exclusive.
     * @param Optional|Document|Serializable|array|stdClass $sortBy Specifies the field or fields to sort the documents within each partition. Uses the same syntax as the $sort stage.
     */
    public function fill(
        Document|Serializable|stdClass|array $output,
        Optional|Document|Serializable|stdClass|array|string $partitionBy = Optional::Undefined,
        Optional|PackedArray|BSONArray|array $partitionByFields = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $sortBy = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::fill($output, $partitionBy, $partitionByFields, $sortBy);

        return $this;
    }

    /**
     * Returns an ordered stream of documents based on the proximity to a geospatial point. Incorporates the functionality of $match, $sort, and $limit for geospatial data. The output documents include an additional distance field and can include a location identifier field.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $near The point for which to find the closest documents.
     * @param Optional|string $distanceField The output field that contains the calculated distance. To specify a field within an embedded document, use dot notation.
     * @param Optional|Decimal128|Int64|float|int $distanceMultiplier The factor to multiply all distances returned by the query. For example, use the distanceMultiplier to convert radians, as returned by a spherical query, to kilometers by multiplying by the radius of the Earth.
     * @param Optional|string $includeLocs This specifies the output field that identifies the location used to calculate the distance. This option is useful when a location field contains multiple locations. To specify a field within an embedded document, use dot notation.
     * @param Optional|string $key Specify the geospatial indexed field to use when calculating the distance.
     * @param Optional|Decimal128|Int64|float|int $maxDistance The maximum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall within the specified distance from the center point.
     * Specify the distance in meters if the specified point is GeoJSON and in radians if the specified point is legacy coordinate pairs.
     * @param Optional|Decimal128|Int64|float|int $minDistance The minimum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall outside the specified distance from the center point.
     * Specify the distance in meters for GeoJSON data and in radians for legacy coordinate pairs.
     * @param Optional|QueryInterface|array $query Limits the results to the documents that match the query. The query syntax is the usual MongoDB read operation query syntax.
     * You cannot specify a $near predicate in the query field of the $geoNear stage.
     * @param Optional|bool $spherical Determines how MongoDB calculates the distance between two points:
     * - When true, MongoDB uses $nearSphere semantics and calculates distances using spherical geometry.
     * - When false, MongoDB uses $near semantics: spherical geometry for 2dsphere indexes and planar geometry for 2d indexes.
     * Default: false.
     */
    public function geoNear(
        Document|Serializable|ResolvesToObject|stdClass|array|string $near,
        Optional|string $distanceField = Optional::Undefined,
        Optional|Decimal128|Int64|int|float $distanceMultiplier = Optional::Undefined,
        Optional|string $includeLocs = Optional::Undefined,
        Optional|string $key = Optional::Undefined,
        Optional|Decimal128|Int64|int|float $maxDistance = Optional::Undefined,
        Optional|Decimal128|Int64|int|float $minDistance = Optional::Undefined,
        Optional|QueryInterface|array $query = Optional::Undefined,
        Optional|bool $spherical = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::geoNear($near, $distanceField, $distanceMultiplier, $includeLocs, $key, $maxDistance, $minDistance, $query, $spherical);

        return $this;
    }

    /**
     * Performs a recursive search on a collection. To each output document, adds a new array field that contains the traversal results of the recursive search for that document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/
     * @param string $from Target collection for the $graphLookup operation to search, recursively matching the connectFromField to the connectToField. The from collection must be in the same database as any other collections used in the operation.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     * @param BSONArray|DateTimeInterface|ExpressionInterface|PackedArray|Type|array|bool|float|int|null|stdClass|string $startWith Expression that specifies the value of the connectFromField with which to start the recursive search. Optionally, startWith may be array of values, each of which is individually followed through the traversal process.
     * @param string $connectFromField Field name whose value $graphLookup uses to recursively match against the connectToField of other documents in the collection. If the value is an array, each element is individually followed through the traversal process.
     * @param string $connectToField Field name in other documents against which to match the value of the field specified by the connectFromField parameter.
     * @param string $as Name of the array field added to each output document. Contains the documents traversed in the $graphLookup stage to reach the document.
     * @param Optional|int $maxDepth Non-negative integral number specifying the maximum recursion depth.
     * @param Optional|string $depthField Name of the field to add to each traversed document in the search path. The value of this field is the recursion depth for the document, represented as a NumberLong. Recursion depth value starts at zero, so the first lookup corresponds to zero depth.
     * @param Optional|QueryInterface|array $restrictSearchWithMatch A document specifying additional conditions for the recursive search. The syntax is identical to query filter syntax.
     */
    public function graphLookup(
        string $from,
        DateTimeInterface|PackedArray|Type|ExpressionInterface|BSONArray|stdClass|array|string|int|float|bool|null $startWith,
        string $connectFromField,
        string $connectToField,
        string $as,
        Optional|int $maxDepth = Optional::Undefined,
        Optional|string $depthField = Optional::Undefined,
        Optional|QueryInterface|array $restrictSearchWithMatch = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::graphLookup($from, $startWith, $connectFromField, $connectToField, $as, $maxDepth, $depthField, $restrictSearchWithMatch);

        return $this;
    }

    /**
     * Groups input documents by a specified identifier expression and applies the accumulator expression(s), if specified, to each group. Consumes all input documents and outputs one document per each distinct group. The output documents only contain the identifier field and, if specified, accumulated fields.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $_id The _id expression specifies the group key. If you specify an _id value of null, or any other constant value, the $group stage returns a single document that aggregates values across all of the input documents.
     * @param AccumulatorInterface|Document|Serializable|array|stdClass ...$field Computed using the accumulator operators.
     */
    public function group(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null $_id,
        Document|Serializable|AccumulatorInterface|stdClass|array ...$field,
    ): static {
        $this->pipeline[] = Stage::group($_id, ...$field);

        return $this;
    }

    /**
     * Returns statistics regarding the use of each index for the collection.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexStats/
     */
    public function indexStats(): static
    {
        $this->pipeline[] = Stage::indexStats();

        return $this;
    }

    /**
     * Passes the first n documents unmodified to the pipeline where n is the specified limit. For each input document, outputs either one document (for the first n documents) or zero documents (after the first n documents).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/limit/
     * @param int $limit
     */
    public function limit(int $limit): static
    {
        $this->pipeline[] = Stage::limit($limit);

        return $this;
    }

    /**
     * Lists all active sessions recently in use on the currently connected mongos or mongod instance. These sessions may have not yet propagated to the system.sessions collection.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listLocalSessions/
     * @param Optional|BSONArray|PackedArray|array $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users.
     * @param Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster.
     */
    public function listLocalSessions(
        Optional|PackedArray|BSONArray|array $users = Optional::Undefined,
        Optional|bool $allUsers = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::listLocalSessions($users, $allUsers);

        return $this;
    }

    /**
     * Lists sampled queries for all collections or a specific collection.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSampledQueries/
     * @param Optional|string $namespace
     */
    public function listSampledQueries(Optional|string $namespace = Optional::Undefined): static
    {
        $this->pipeline[] = Stage::listSampledQueries($namespace);

        return $this;
    }

    /**
     * Returns information about existing Atlas Search indexes on a specified collection.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/
     * @param Optional|string $id The id of the index to return information about.
     * @param Optional|string $name The name of the index to return information about.
     */
    public function listSearchIndexes(
        Optional|string $id = Optional::Undefined,
        Optional|string $name = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::listSearchIndexes($id, $name);

        return $this;
    }

    /**
     * Lists all sessions that have been active long enough to propagate to the system.sessions collection.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSessions/
     * @param Optional|BSONArray|PackedArray|array $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users.
     * @param Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster.
     */
    public function listSessions(
        Optional|PackedArray|BSONArray|array $users = Optional::Undefined,
        Optional|bool $allUsers = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::listSessions($users, $allUsers);

        return $this;
    }

    /**
     * Performs a left outer join to another collection in the same database to filter in documents from the "joined" collection for processing.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/
     * @param string $as Specifies the name of the new array field to add to the input documents. The new array field contains the matching documents from the from collection. If the specified name already exists in the input document, the existing field is overwritten.
     * @param Optional|string $from Specifies the collection in the same database to perform the join with.
     * from is optional, you can use a $documents stage in a $lookup stage instead. For an example, see Use a $documents Stage in a $lookup Stage.
     * Starting in MongoDB 5.1, the collection specified in the from parameter can be sharded.
     * @param Optional|string $localField Specifies the field from the documents input to the $lookup stage. $lookup performs an equality match on the localField to the foreignField from the documents of the from collection. If an input document does not contain the localField, the $lookup treats the field as having a value of null for matching purposes.
     * @param Optional|string $foreignField Specifies the field from the documents in the from collection. $lookup performs an equality match on the foreignField to the localField from the input documents. If a document in the from collection does not contain the foreignField, the $lookup treats the value as null for matching purposes.
     * @param Optional|Document|Serializable|array|stdClass $let Specifies variables to use in the pipeline stages. Use the variable expressions to access the fields from the joined collection's documents that are input to the pipeline.
     * @param Optional|BSONArray|PackedArray|Pipeline|array $pipeline Specifies the pipeline to run on the joined collection. The pipeline determines the resulting documents from the joined collection. To return all documents, specify an empty pipeline [].
     * The pipeline cannot include the $out stage or the $merge stage. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     * The pipeline cannot directly access the joined document fields. Instead, define variables for the joined document fields using the let option and then reference the variables in the pipeline stages.
     */
    public function lookup(
        string $as,
        Optional|string $from = Optional::Undefined,
        Optional|string $localField = Optional::Undefined,
        Optional|string $foreignField = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $let = Optional::Undefined,
        Optional|PackedArray|Pipeline|BSONArray|array $pipeline = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::lookup($as, $from, $localField, $foreignField, $let, $pipeline);

        return $this;
    }

    /**
     * Writes the resulting documents of the aggregation pipeline to a collection. The stage can incorporate (insert new documents, merge documents, replace documents, keep existing documents, fail the operation, process documents with a custom update pipeline) the results into an output collection. To use the $merge stage, it must be the last stage in the pipeline.
     * New in MongoDB 4.2.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/
     * @param Document|Serializable|array|stdClass|string $into The output collection.
     * @param Optional|BSONArray|PackedArray|array|string $on Field or fields that act as a unique identifier for a document. The identifier determines if a results document matches an existing document in the output collection.
     * @param Optional|Document|Serializable|array|stdClass $let Specifies variables for use in the whenMatched pipeline.
     * @param Optional|BSONArray|PackedArray|Pipeline|array|string $whenMatched The behavior of $merge if a result document and an existing document in the collection have the same value for the specified on field(s).
     * @param Optional|string $whenNotMatched The behavior of $merge if a result document does not match an existing document in the out collection.
     */
    public function merge(
        Document|Serializable|stdClass|array|string $into,
        Optional|PackedArray|BSONArray|array|string $on = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $let = Optional::Undefined,
        Optional|PackedArray|Pipeline|BSONArray|array|string $whenMatched = Optional::Undefined,
        Optional|string $whenNotMatched = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::merge($into, $on, $let, $whenMatched, $whenNotMatched);

        return $this;
    }

    /**
     * Writes the resulting documents of the aggregation pipeline to a collection. To use the $out stage, it must be the last stage in the pipeline.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/
     * @param Document|Serializable|array|stdClass|string $coll Target database name to write documents from $out to.
     */
    public function out(Document|Serializable|stdClass|array|string $coll): static
    {
        $this->pipeline[] = Stage::out($coll);

        return $this;
    }

    /**
     * Returns plan cache information for a collection.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/planCacheStats/
     */
    public function planCacheStats(): static
    {
        $this->pipeline[] = Stage::planCacheStats();

        return $this;
    }

    /**
     * Reshapes each document in the stream, such as by adding new fields or removing existing fields. For each input document, outputs one document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$specification
     */
    public function project(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null ...$specification,
    ): static {
        $this->pipeline[] = Stage::project(...$specification);

        return $this;
    }

    /**
     * Reshapes each document in the stream by restricting the content for each document based on information stored in the documents themselves. Incorporates the functionality of $project and $match. Can be used to implement field level redaction. For each input document, outputs either one or zero documents.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public function redact(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null $expression,
    ): static {
        $this->pipeline[] = Stage::redact($expression);

        return $this;
    }

    /**
     * Replaces a document with the specified embedded document. The operation replaces all existing fields in the input document, including the _id field. Specify a document embedded in the input document to promote the embedded document to the top level.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $newRoot
     */
    public function replaceRoot(Document|Serializable|ResolvesToObject|stdClass|array|string $newRoot): static
    {
        $this->pipeline[] = Stage::replaceRoot($newRoot);

        return $this;
    }

    /**
     * Replaces a document with the specified embedded document. The operation replaces all existing fields in the input document, including the _id field. Specify a document embedded in the input document to promote the embedded document to the top level.
     * Alias for $replaceRoot.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $expression
     */
    public function replaceWith(Document|Serializable|ResolvesToObject|stdClass|array|string $expression): static
    {
        $this->pipeline[] = Stage::replaceWith($expression);

        return $this;
    }

    /**
     * Randomly selects the specified number of documents from its input.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sample/
     * @param int $size The number of documents to randomly select.
     */
    public function sample(int $size): static
    {
        $this->pipeline[] = Stage::sample($size);

        return $this;
    }

    /**
     * Performs a full-text search of the field or fields in an Atlas collection.
     * NOTE: $search is only available for MongoDB Atlas clusters, and is not available for self-managed deployments.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/search/
     * @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator Operator to search with.  You can provide a specific operator or use
     * the compound operator to run a compound query with multiple operators.
     * @param Optional|string $index Name of the Atlas Search index to use. If omitted, defaults to "default".
     * @param Optional|Document|Serializable|array|stdClass $highlight Specifies the highlighting options for displaying search terms in their original context.
     * @param Optional|bool $concurrent Parallelize search across segments on dedicated search nodes.
     * If you don't have separate search nodes on your cluster,
     * Atlas Search ignores this flag. If omitted, defaults to false.
     * @param Optional|Document|Serializable|array|stdClass $count Document that specifies the count options for retrieving a count of the results.
     * @param Optional|string $searchAfter Reference point for retrieving results. searchAfter returns documents starting immediately following the specified reference point.
     * @param Optional|string $searchBefore Reference point for retrieving results. searchBefore returns documents starting immediately before the specified reference point.
     * @param Optional|bool $scoreDetails Flag that specifies whether to retrieve a detailed breakdown of the score for the documents in the results. If omitted, defaults to false.
     * @param Optional|Document|Serializable|array|stdClass $sort Document that specifies the fields to sort the Atlas Search results by in ascending or descending order.
     * @param Optional|bool $returnStoredSource Flag that specifies whether to perform a full document lookup on the backend database or return only stored source fields directly from Atlas Search.
     * @param Optional|Document|Serializable|array|stdClass $tracking Document that specifies the tracking option to retrieve analytics information on the search terms.
     */
    public function search(
        Document|Serializable|SearchOperatorInterface|stdClass|array $operator,
        Optional|string $index = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $highlight = Optional::Undefined,
        Optional|bool $concurrent = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $count = Optional::Undefined,
        Optional|string $searchAfter = Optional::Undefined,
        Optional|string $searchBefore = Optional::Undefined,
        Optional|bool $scoreDetails = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $sort = Optional::Undefined,
        Optional|bool $returnStoredSource = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $tracking = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::search($operator, $index, $highlight, $concurrent, $count, $searchAfter, $searchBefore, $scoreDetails, $sort, $returnStoredSource, $tracking);

        return $this;
    }

    /**
     * Returns different types of metadata result documents for the Atlas Search query against an Atlas collection.
     * NOTE: $searchMeta is only available for MongoDB Atlas clusters running MongoDB v4.4.9 or higher, and is not available for self-managed deployments.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/searchMeta/
     * @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator Operator to search with.  You can provide a specific operator or use
     * the compound operator to run a compound query with multiple operators.
     * @param Optional|string $index Name of the Atlas Search index to use. If omitted, defaults to default.
     * @param Optional|Document|Serializable|array|stdClass $count Document that specifies the count options for retrieving a count of the results.
     */
    public function searchMeta(
        Document|Serializable|SearchOperatorInterface|stdClass|array $operator,
        Optional|string $index = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $count = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::searchMeta($operator, $index, $count);

        return $this;
    }

    /**
     * Adds new fields to documents. Outputs documents that contain all existing fields from the input documents and newly added fields.
     * Alias for $addFields.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public function set(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null ...$field,
    ): static {
        $this->pipeline[] = Stage::set(...$field);

        return $this;
    }

    /**
     * Groups documents into windows and applies one or more operators to the documents in each window.
     * New in MongoDB 5.0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
     * @param Document|Serializable|array|stdClass $sortBy Specifies the field(s) to sort the documents by in the partition. Uses the same syntax as the $sort stage. Default is no sorting.
     * @param Document|Serializable|array|stdClass $output Specifies the field(s) to append to the documents in the output returned by the $setWindowFields stage. Each field is set to the result returned by the window operator.
     * A field can contain dots to specify embedded document fields and array fields. The semantics for the embedded document dotted notation in the $setWindowFields stage are the same as the $addFields and $set stages.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $partitionBy Specifies an expression to group the documents. In the $setWindowFields stage, the group of documents is known as a partition. Default is one partition for the entire collection.
     */
    public function setWindowFields(
        Document|Serializable|stdClass|array $sortBy,
        Document|Serializable|stdClass|array $output,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null $partitionBy = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::setWindowFields($sortBy, $output, $partitionBy);

        return $this;
    }

    /**
     * Provides data and size distribution information on sharded collections.
     * New in MongoDB 6.0.3.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shardedDataDistribution/
     */
    public function shardedDataDistribution(): static
    {
        $this->pipeline[] = Stage::shardedDataDistribution();

        return $this;
    }

    /**
     * Skips the first n documents where n is the specified skip number and passes the remaining documents unmodified to the pipeline. For each input document, outputs either zero documents (for the first n documents) or one document (if after the first n documents).
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/skip/
     * @param int $skip
     */
    public function skip(int $skip): static
    {
        $this->pipeline[] = Stage::skip($skip);

        return $this;
    }

    /**
     * Reorders the document stream by a specified sort key. Only the order changes; the documents remain unmodified. For each input document, outputs one document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/
     * @param DateTimeInterface|ExpressionInterface|Sort|Type|array|bool|float|int|null|stdClass|string ...$sort
     */
    public function sort(
        DateTimeInterface|Type|ExpressionInterface|Sort|stdClass|array|string|int|float|bool|null ...$sort,
    ): static {
        $this->pipeline[] = Stage::sort(...$sort);

        return $this;
    }

    /**
     * Groups incoming documents based on the value of a specified expression, then computes the count of documents in each distinct group.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortByCount/
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public function sortByCount(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|string|int|float|bool|null $expression,
    ): static {
        $this->pipeline[] = Stage::sortByCount($expression);

        return $this;
    }

    /**
     * Performs a union of two collections; i.e. combines pipeline results from two collections into a single result set.
     * New in MongoDB 4.4.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unionWith/
     * @param string $coll The collection or view whose pipeline results you wish to include in the result set.
     * @param Optional|BSONArray|PackedArray|Pipeline|array $pipeline An aggregation pipeline to apply to the specified coll.
     * The pipeline cannot include the $out and $merge stages. Starting in v6.0, the pipeline can contain the Atlas Search $search stage as the first stage inside the pipeline.
     */
    public function unionWith(
        string $coll,
        Optional|PackedArray|Pipeline|BSONArray|array $pipeline = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::unionWith($coll, $pipeline);

        return $this;
    }

    /**
     * Removes or excludes fields from documents.
     * Alias for $project stage that removes or excludes fields.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/
     * @no-named-arguments
     * @param FieldPath|string ...$field
     */
    public function unset(FieldPath|string ...$field): static
    {
        $this->pipeline[] = Stage::unset(...$field);

        return $this;
    }

    /**
     * Deconstructs an array field from the input documents to output a document for each element. Each output document replaces the array with an element value. For each input document, outputs n documents where n is the number of array elements and can be zero for an empty array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/
     * @param ArrayFieldPath|string $path Field path to an array field.
     * @param Optional|string $includeArrayIndex The name of a new field to hold the array index of the element. The name cannot start with a dollar sign $.
     * @param Optional|bool $preserveNullAndEmptyArrays If true, if the path is null, missing, or an empty array, $unwind outputs the document.
     * If false, if path is null, missing, or an empty array, $unwind does not output a document.
     * The default value is false.
     */
    public function unwind(
        ArrayFieldPath|string $path,
        Optional|string $includeArrayIndex = Optional::Undefined,
        Optional|bool $preserveNullAndEmptyArrays = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::unwind($path, $includeArrayIndex, $preserveNullAndEmptyArrays);

        return $this;
    }

    /**
     * The $vectorSearch stage performs an ANN or ENN search on a vector in the specified field.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-stage/
     * @param string $index Name of the Atlas Vector Search index to use.
     * @param int $limit Number of documents to return in the results. This value can't exceed the value of numCandidates if you specify numCandidates.
     * @param string $path Indexed vector type field to search.
     * @param BSONArray|PackedArray|array $queryVector Array of numbers that represent the query vector. The number type must match the indexed field value type.
     * @param Optional|bool $exact This is required if numCandidates is omitted. false to run ANN search. true to run ENN search.
     * @param Optional|QueryInterface|array $filter Any match query that compares an indexed field with a boolean, date, objectId, number (not decimals), string, or UUID to use as a pre-filter.
     * @param Optional|int $numCandidates This field is required if exact is false or omitted.
     * Number of nearest neighbors to use during the search. Value must be less than or equal to (<=) 10000. You can't specify a number less than the number of documents to return (limit).
     */
    public function vectorSearch(
        string $index,
        int $limit,
        string $path,
        PackedArray|BSONArray|array $queryVector,
        Optional|bool $exact = Optional::Undefined,
        Optional|QueryInterface|array $filter = Optional::Undefined,
        Optional|int $numCandidates = Optional::Undefined,
    ): static {
        $this->pipeline[] = Stage::vectorSearch($index, $limit, $path, $queryVector, $exact, $filter, $numCandidates);

        return $this;
    }
}
