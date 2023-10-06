<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Query\AllQuery;
use MongoDB\Builder\Query\AndQuery;
use MongoDB\Builder\Query\BitsAllClearQuery;
use MongoDB\Builder\Query\BitsAllSetQuery;
use MongoDB\Builder\Query\BitsAnyClearQuery;
use MongoDB\Builder\Query\BitsAnySetQuery;
use MongoDB\Builder\Query\BoxQuery;
use MongoDB\Builder\Query\CenterQuery;
use MongoDB\Builder\Query\CenterSphereQuery;
use MongoDB\Builder\Query\CommentQuery;
use MongoDB\Builder\Query\ElemMatchQuery;
use MongoDB\Builder\Query\EqQuery;
use MongoDB\Builder\Query\ExistsQuery;
use MongoDB\Builder\Query\ExprQuery;
use MongoDB\Builder\Query\GeoIntersectsQuery;
use MongoDB\Builder\Query\GeoWithinQuery;
use MongoDB\Builder\Query\GeometryQuery;
use MongoDB\Builder\Query\GtQuery;
use MongoDB\Builder\Query\GteQuery;
use MongoDB\Builder\Query\InQuery;
use MongoDB\Builder\Query\JsonSchemaQuery;
use MongoDB\Builder\Query\LtQuery;
use MongoDB\Builder\Query\LteQuery;
use MongoDB\Builder\Query\MaxDistanceQuery;
use MongoDB\Builder\Query\MetaQuery;
use MongoDB\Builder\Query\MinDistanceQuery;
use MongoDB\Builder\Query\ModQuery;
use MongoDB\Builder\Query\NaturalQuery;
use MongoDB\Builder\Query\NeQuery;
use MongoDB\Builder\Query\NearQuery;
use MongoDB\Builder\Query\NearSphereQuery;
use MongoDB\Builder\Query\NinQuery;
use MongoDB\Builder\Query\NorQuery;
use MongoDB\Builder\Query\NotQuery;
use MongoDB\Builder\Query\OrQuery;
use MongoDB\Builder\Query\PolygonQuery;
use MongoDB\Builder\Query\QueryInterface;
use MongoDB\Builder\Query\RandQuery;
use MongoDB\Builder\Query\RegexQuery;
use MongoDB\Builder\Query\SizeQuery;
use MongoDB\Builder\Query\SliceQuery;
use MongoDB\Builder\Query\TextQuery;
use MongoDB\Builder\Query\TypeQuery;
use MongoDB\Builder\Query\WhereQuery;
use MongoDB\Model\BSONArray;
use stdClass;

final class Query
{
    /**
     * Matches arrays that contain all elements specified in the query.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/all/
     * @param mixed ...$value
     */
    public static function all(mixed ...$value): AllQuery
    {
        return new AllQuery(...$value);
    }

    /**
     * Joins query clauses with a logical AND returns all documents that match the conditions of both clauses.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/and/
     * @param QueryInterface|array|stdClass ...$expression
     */
    public static function and(QueryInterface|stdClass|array ...$expression): AndQuery
    {
        return new AndQuery(...$expression);
    }

    /**
     * Matches numeric or binary values in which a set of bit positions all have a value of 0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllClear/
     * @param BSONArray|Binary|Int64|PackedArray|int|list|non-empty-string $bitmask
     */
    public static function bitsAllClear(
        Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask,
    ): BitsAllClearQuery
    {
        return new BitsAllClearQuery($bitmask);
    }

    /**
     * Matches numeric or binary values in which a set of bit positions all have a value of 1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllSet/
     * @param BSONArray|Binary|Int64|PackedArray|int|list|non-empty-string $bitmask
     */
    public static function bitsAllSet(Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask): BitsAllSetQuery
    {
        return new BitsAllSetQuery($bitmask);
    }

    /**
     * Matches numeric or binary values in which any bit from a set of bit positions has a value of 0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnyClear/
     * @param BSONArray|Binary|Int64|PackedArray|int|list|non-empty-string $bitmask
     */
    public static function bitsAnyClear(
        Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask,
    ): BitsAnyClearQuery
    {
        return new BitsAnyClearQuery($bitmask);
    }

    /**
     * Matches numeric or binary values in which any bit from a set of bit positions has a value of 1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnySet/
     * @param BSONArray|Binary|Int64|PackedArray|int|list|non-empty-string $bitmask
     */
    public static function bitsAnySet(Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask): BitsAnySetQuery
    {
        return new BitsAnySetQuery($bitmask);
    }

    /**
     * Specifies a rectangular box using legacy coordinate pairs for $geoWithin queries. The 2d index supports $box.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/box/
     * @param BSONArray|PackedArray|list $value
     */
    public static function box(PackedArray|BSONArray|array $value): BoxQuery
    {
        return new BoxQuery($value);
    }

    /**
     * Specifies a circle using legacy coordinate pairs to $geoWithin queries when using planar geometry. The 2d index supports $center.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/center/
     * @param BSONArray|PackedArray|list $value
     */
    public static function center(PackedArray|BSONArray|array $value): CenterQuery
    {
        return new CenterQuery($value);
    }

    /**
     * Specifies a circle using either legacy coordinate pairs or GeoJSON format for $geoWithin queries when using spherical geometry. The 2dsphere and 2d indexes support $centerSphere.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/centerSphere/
     * @param BSONArray|PackedArray|list $value
     */
    public static function centerSphere(PackedArray|BSONArray|array $value): CenterSphereQuery
    {
        return new CenterSphereQuery($value);
    }

    /**
     * Adds a comment to a query predicate.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/comment/
     * @param non-empty-string $comment
     */
    public static function comment(string $comment): CommentQuery
    {
        return new CommentQuery($comment);
    }

    /**
     * Projects the first element in an array that matches the specified $elemMatch condition.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/
     * @param Document|Serializable|array|stdClass $queries
     */
    public static function elemMatch(Document|Serializable|stdClass|array $queries): ElemMatchQuery
    {
        return new ElemMatchQuery($queries);
    }

    /**
     * Matches values that are equal to a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/
     * @param mixed $value
     */
    public static function eq(mixed $value): EqQuery
    {
        return new EqQuery($value);
    }

    /**
     * Matches documents that have the specified field.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/exists/
     * @param bool $exists
     */
    public static function exists(bool $exists): ExistsQuery
    {
        return new ExistsQuery($exists);
    }

    /**
     * Allows use of aggregation expressions within the query language.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/expr/
     * @param ExpressionInterface|mixed $expression
     */
    public static function expr(mixed $expression): ExprQuery
    {
        return new ExprQuery($expression);
    }

    /**
     * Selects geometries that intersect with a GeoJSON geometry. The 2dsphere index supports $geoIntersects.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoIntersects/
     * @param array|stdClass $geometry
     */
    public static function geoIntersects(stdClass|array $geometry): GeoIntersectsQuery
    {
        return new GeoIntersectsQuery($geometry);
    }

    /**
     * Specifies a geometry in GeoJSON format to geospatial query operators.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geometry/
     * @param non-empty-string $type
     * @param BSONArray|PackedArray|list $coordinates
     * @param Document|Serializable|array|stdClass $crs
     */
    public static function geometry(
        string $type,
        PackedArray|BSONArray|array $coordinates,
        Document|Serializable|stdClass|array $crs,
    ): GeometryQuery
    {
        return new GeometryQuery($type, $coordinates, $crs);
    }

    /**
     * Selects geometries within a bounding GeoJSON geometry. The 2dsphere and 2d indexes support $geoWithin.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/
     * @param array|stdClass $geometry
     */
    public static function geoWithin(stdClass|array $geometry): GeoWithinQuery
    {
        return new GeoWithinQuery($geometry);
    }

    /**
     * Matches values that are greater than a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/gt/
     * @param mixed $value
     */
    public static function gt(mixed $value): GtQuery
    {
        return new GtQuery($value);
    }

    /**
     * Matches values that are greater than or equal to a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/gte/
     * @param mixed $value
     */
    public static function gte(mixed $value): GteQuery
    {
        return new GteQuery($value);
    }

    /**
     * Matches any of the values specified in an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/in/
     * @param BSONArray|PackedArray|list $value
     */
    public static function in(PackedArray|BSONArray|array $value): InQuery
    {
        return new InQuery($value);
    }

    /**
     * Validate documents against the given JSON Schema.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/jsonSchema/
     * @param Document|Serializable|array|stdClass $schema
     */
    public static function jsonSchema(Document|Serializable|stdClass|array $schema): JsonSchemaQuery
    {
        return new JsonSchemaQuery($schema);
    }

    /**
     * Matches values that are less than a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/lt/
     * @param mixed $value
     */
    public static function lt(mixed $value): LtQuery
    {
        return new LtQuery($value);
    }

    /**
     * Matches values that are less than or equal to a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/lte/
     * @param mixed $value
     */
    public static function lte(mixed $value): LteQuery
    {
        return new LteQuery($value);
    }

    /**
     * Specifies a maximum distance to limit the results of $near and $nearSphere queries. The 2dsphere and 2d indexes support $maxDistance.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/maxDistance/
     * @param Int64|float|int $value
     */
    public static function maxDistance(Int64|float|int $value): MaxDistanceQuery
    {
        return new MaxDistanceQuery($value);
    }

    /**
     * Projects the available per-document metadata.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/meta/
     */
    public static function meta(): MetaQuery
    {
        return new MetaQuery();
    }

    /**
     * Specifies a minimum distance to limit the results of $near and $nearSphere queries. For use with 2dsphere index only.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/minDistance/
     * @param Int64|float|int $value
     */
    public static function minDistance(Int64|float|int $value): MinDistanceQuery
    {
        return new MinDistanceQuery($value);
    }

    /**
     * Performs a modulo operation on the value of a field and selects documents with a specified result.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/
     * @param Int64|int $divisor
     * @param Int64|int $remainder
     */
    public static function mod(Int64|int $divisor, Int64|int $remainder): ModQuery
    {
        return new ModQuery($divisor, $remainder);
    }

    /**
     * A special hint that can be provided via the sort() or hint() methods that can be used to force either a forward or reverse collection scan.
     *
     * @see https://www.mongodb.com/docs/v7.0/reference/operator/meta/natural/
     */
    public static function natural(): NaturalQuery
    {
        return new NaturalQuery();
    }

    /**
     * Matches all values that are not equal to a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/ne/
     * @param mixed $value
     */
    public static function ne(mixed $value): NeQuery
    {
        return new NeQuery($value);
    }

    /**
     * Returns geospatial objects in proximity to a point. Requires a geospatial index. The 2dsphere and 2d indexes support $near.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/near/
     * @param array|stdClass $geometry
     * @param Int64|Optional|int $maxDistance Distance in meters. Limits the results to those documents that are at most the specified distance from the center point.
     * @param Int64|Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public static function near(
        stdClass|array $geometry,
        Int64|Optional|int $maxDistance = Optional::Undefined,
        Int64|Optional|int $minDistance = Optional::Undefined,
    ): NearQuery
    {
        return new NearQuery($geometry, $maxDistance, $minDistance);
    }

    /**
     * Returns geospatial objects in proximity to a point on a sphere. Requires a geospatial index. The 2dsphere and 2d indexes support $nearSphere.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nearSphere/
     * @param array|stdClass $geometry
     * @param Int64|Optional|int $maxDistance Distance in meters.
     * @param Int64|Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public static function nearSphere(
        stdClass|array $geometry,
        Int64|Optional|int $maxDistance = Optional::Undefined,
        Int64|Optional|int $minDistance = Optional::Undefined,
    ): NearSphereQuery
    {
        return new NearSphereQuery($geometry, $maxDistance, $minDistance);
    }

    /**
     * Matches none of the values specified in an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nin/
     * @param BSONArray|PackedArray|list $value
     */
    public static function nin(PackedArray|BSONArray|array $value): NinQuery
    {
        return new NinQuery($value);
    }

    /**
     * Joins query clauses with a logical NOR returns all documents that fail to match both clauses.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nor/
     * @param QueryInterface|array|stdClass ...$expression
     */
    public static function nor(QueryInterface|stdClass|array ...$expression): NorQuery
    {
        return new NorQuery(...$expression);
    }

    /**
     * Inverts the effect of a query expression and returns documents that do not match the query expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/
     * @param QueryInterface|array|stdClass $expression
     */
    public static function not(QueryInterface|stdClass|array $expression): NotQuery
    {
        return new NotQuery($expression);
    }

    /**
     * Joins query clauses with a logical OR returns all documents that match the conditions of either clause.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/or/
     * @param QueryInterface|array|stdClass ...$expression
     */
    public static function or(QueryInterface|stdClass|array ...$expression): OrQuery
    {
        return new OrQuery(...$expression);
    }

    /**
     * Specifies a polygon to using legacy coordinate pairs for $geoWithin queries. The 2d index supports $center.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/polygon/
     * @param BSONArray|PackedArray|list $points
     */
    public static function polygon(PackedArray|BSONArray|array $points): PolygonQuery
    {
        return new PolygonQuery($points);
    }

    /**
     * Generates a random float between 0 and 1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/rand/
     */
    public static function rand(): RandQuery
    {
        return new RandQuery();
    }

    /**
     * Selects documents where values match a specified regular expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/regex/
     * @param Regex $regex
     */
    public static function regex(Regex $regex): RegexQuery
    {
        return new RegexQuery($regex);
    }

    /**
     * Selects documents if the array field is a specified size.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/size/
     * @param Int64|int $value
     */
    public static function size(Int64|int $value): SizeQuery
    {
        return new SizeQuery($value);
    }

    /**
     * Limits the number of elements projected from an array. Supports skip and limit slices.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/slice/
     * @param Int64|int $limit
     * @param Int64|int $skip
     */
    public static function slice(Int64|int $limit, Int64|int $skip): SliceQuery
    {
        return new SliceQuery($limit, $skip);
    }

    /**
     * Performs text search.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/
     * @param non-empty-string $search A string of terms that MongoDB parses and uses to query the text index. MongoDB performs a logical OR search of the terms unless specified as a phrase.
     * @param Optional|non-empty-string $language The language that determines the list of stop words for the search and the rules for the stemmer and tokenizer. If not specified, the search uses the default language of the index.
     * If you specify a default_language value of none, then the text index parses through each word in the field, including stop words, and ignores suffix stemming.
     * @param Optional|bool $caseSensitive A boolean flag to enable or disable case sensitive search. Defaults to false; i.e. the search defers to the case insensitivity of the text index.
     * @param Optional|bool $diacriticSensitive A boolean flag to enable or disable diacritic sensitive search against version 3 text indexes. Defaults to false; i.e. the search defers to the diacritic insensitivity of the text index.
     * Text searches against earlier versions of the text index are inherently diacritic sensitive and cannot be diacritic insensitive. As such, the $diacriticSensitive option has no effect with earlier versions of the text index.
     */
    public static function text(
        string $search,
        Optional|string $language = Optional::Undefined,
        Optional|bool $caseSensitive = Optional::Undefined,
        Optional|bool $diacriticSensitive = Optional::Undefined,
    ): TextQuery
    {
        return new TextQuery($search, $language, $caseSensitive, $diacriticSensitive);
    }

    /**
     * Selects documents if a field is of the specified type.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/
     * @param BSONArray|Int64|PackedArray|int|list|non-empty-string $type
     */
    public static function type(Int64|PackedArray|BSONArray|array|int|string $type): TypeQuery
    {
        return new TypeQuery($type);
    }

    /**
     * Matches documents that satisfy a JavaScript expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/where/
     * @param non-empty-string $function
     */
    public static function where(string $function): WhereQuery
    {
        return new WhereQuery($function);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
