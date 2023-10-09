<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Expression\ResolvesToDecimal;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
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
use MongoDB\Builder\Query\RandQuery;
use MongoDB\Builder\Query\RegexQuery;
use MongoDB\Builder\Query\SizeQuery;
use MongoDB\Builder\Query\SliceQuery;
use MongoDB\Builder\Query\TextQuery;
use MongoDB\Builder\Query\TypeQuery;
use MongoDB\Builder\Query\WhereQuery;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Model\BSONArray;
use stdClass;

final class Query
{
    /**
     * Matches arrays that contain all elements specified in the query.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/all/
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass ...$value
     */
    public static function all(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string ...$value,
    ): AllQuery
    {
        return new AllQuery(...$value);
    }

    /**
     * Joins query clauses with a logical AND returns all documents that match the conditions of both clauses.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/and/
     * @param Document|QueryInterface|Serializable|array|stdClass ...$expression
     */
    public static function and(Document|Serializable|QueryInterface|stdClass|array ...$expression): AndQuery
    {
        return new AndQuery(...$expression);
    }

    /**
     * Matches numeric or binary values in which a set of bit positions all have a value of 0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllClear/
     * @param BSONArray|Binary|PackedArray|array|int|non-empty-string $bitmask
     */
    public static function bitsAllClear(Binary|PackedArray|BSONArray|array|int|string $bitmask): BitsAllClearQuery
    {
        return new BitsAllClearQuery($bitmask);
    }

    /**
     * Matches numeric or binary values in which a set of bit positions all have a value of 1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllSet/
     * @param BSONArray|Binary|PackedArray|array|int|non-empty-string $bitmask
     */
    public static function bitsAllSet(Binary|PackedArray|BSONArray|array|int|string $bitmask): BitsAllSetQuery
    {
        return new BitsAllSetQuery($bitmask);
    }

    /**
     * Matches numeric or binary values in which any bit from a set of bit positions has a value of 0.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnyClear/
     * @param BSONArray|Binary|PackedArray|array|int|non-empty-string $bitmask
     */
    public static function bitsAnyClear(Binary|PackedArray|BSONArray|array|int|string $bitmask): BitsAnyClearQuery
    {
        return new BitsAnyClearQuery($bitmask);
    }

    /**
     * Matches numeric or binary values in which any bit from a set of bit positions has a value of 1.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnySet/
     * @param BSONArray|Binary|PackedArray|array|int|non-empty-string $bitmask
     */
    public static function bitsAnySet(Binary|PackedArray|BSONArray|array|int|string $bitmask): BitsAnySetQuery
    {
        return new BitsAnySetQuery($bitmask);
    }

    /**
     * Specifies a rectangular box using legacy coordinate pairs for $geoWithin queries. The 2d index supports $box.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/box/
     * @param BSONArray|PackedArray|array $value
     */
    public static function box(PackedArray|BSONArray|array $value): BoxQuery
    {
        return new BoxQuery($value);
    }

    /**
     * Specifies a circle using legacy coordinate pairs to $geoWithin queries when using planar geometry. The 2d index supports $center.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/center/
     * @param BSONArray|PackedArray|array $value
     */
    public static function center(PackedArray|BSONArray|array $value): CenterQuery
    {
        return new CenterQuery($value);
    }

    /**
     * Specifies a circle using either legacy coordinate pairs or GeoJSON format for $geoWithin queries when using spherical geometry. The 2dsphere and 2d indexes support $centerSphere.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/centerSphere/
     * @param BSONArray|PackedArray|array $value
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
     * @param Document|QueryInterface|Serializable|array|stdClass $queries
     */
    public static function elemMatch(Document|Serializable|QueryInterface|stdClass|array $queries): ElemMatchQuery
    {
        return new ElemMatchQuery($queries);
    }

    /**
     * Matches values that are equal to a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value
     */
    public static function eq(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ): EqQuery
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
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public static function expr(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $expression,
    ): ExprQuery
    {
        return new ExprQuery($expression);
    }

    /**
     * Selects geometries that intersect with a GeoJSON geometry. The 2dsphere index supports $geoIntersects.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoIntersects/
     * @param Document|Serializable|array|stdClass $geometry
     */
    public static function geoIntersects(Document|Serializable|stdClass|array $geometry): GeoIntersectsQuery
    {
        return new GeoIntersectsQuery($geometry);
    }

    /**
     * Specifies a geometry in GeoJSON format to geospatial query operators.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geometry/
     * @param non-empty-string $type
     * @param BSONArray|PackedArray|array $coordinates
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
     * @param Document|Serializable|array|stdClass $geometry
     */
    public static function geoWithin(Document|Serializable|stdClass|array $geometry): GeoWithinQuery
    {
        return new GeoWithinQuery($geometry);
    }

    /**
     * Matches values that are greater than a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/gt/
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value
     */
    public static function gt(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ): GtQuery
    {
        return new GtQuery($value);
    }

    /**
     * Matches values that are greater than or equal to a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/gte/
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value
     */
    public static function gte(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ): GteQuery
    {
        return new GteQuery($value);
    }

    /**
     * Matches any of the values specified in an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/in/
     * @param BSONArray|PackedArray|array $value
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
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value
     */
    public static function lt(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ): LtQuery
    {
        return new LtQuery($value);
    }

    /**
     * Matches values that are less than or equal to a specified value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/lte/
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value
     */
    public static function lte(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ): LteQuery
    {
        return new LteQuery($value);
    }

    /**
     * Specifies a maximum distance to limit the results of $near and $nearSphere queries. The 2dsphere and 2d indexes support $maxDistance.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/maxDistance/
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $value
     */
    public static function maxDistance(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $value,
    ): MaxDistanceQuery
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
     * @param int $divisor
     * @param int $remainder
     */
    public static function mod(int $divisor, int $remainder): ModQuery
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
     * @param BSONArray|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $value
     */
    public static function ne(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|BSONArray|stdClass|array|bool|float|int|null|string $value,
    ): NeQuery
    {
        return new NeQuery($value);
    }

    /**
     * Returns geospatial objects in proximity to a point. Requires a geospatial index. The 2dsphere and 2d indexes support $near.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/near/
     * @param Document|Serializable|array|stdClass $geometry
     * @param Optional|int $maxDistance Distance in meters. Limits the results to those documents that are at most the specified distance from the center point.
     * @param Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public static function near(
        Document|Serializable|stdClass|array $geometry,
        Optional|int $maxDistance = Optional::Undefined,
        Optional|int $minDistance = Optional::Undefined,
    ): NearQuery
    {
        return new NearQuery($geometry, $maxDistance, $minDistance);
    }

    /**
     * Returns geospatial objects in proximity to a point on a sphere. Requires a geospatial index. The 2dsphere and 2d indexes support $nearSphere.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nearSphere/
     * @param Document|Serializable|array|stdClass $geometry
     * @param Optional|int $maxDistance Distance in meters.
     * @param Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public static function nearSphere(
        Document|Serializable|stdClass|array $geometry,
        Optional|int $maxDistance = Optional::Undefined,
        Optional|int $minDistance = Optional::Undefined,
    ): NearSphereQuery
    {
        return new NearSphereQuery($geometry, $maxDistance, $minDistance);
    }

    /**
     * Matches none of the values specified in an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nin/
     * @param BSONArray|PackedArray|array $value
     */
    public static function nin(PackedArray|BSONArray|array $value): NinQuery
    {
        return new NinQuery($value);
    }

    /**
     * Joins query clauses with a logical NOR returns all documents that fail to match both clauses.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nor/
     * @param Document|QueryInterface|Serializable|array|stdClass ...$expression
     */
    public static function nor(Document|Serializable|QueryInterface|stdClass|array ...$expression): NorQuery
    {
        return new NorQuery(...$expression);
    }

    /**
     * Inverts the effect of a query expression and returns documents that do not match the query expression.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/
     * @param Document|QueryInterface|Serializable|array|stdClass $expression
     */
    public static function not(Document|Serializable|QueryInterface|stdClass|array $expression): NotQuery
    {
        return new NotQuery($expression);
    }

    /**
     * Joins query clauses with a logical OR returns all documents that match the conditions of either clause.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/or/
     * @param Document|QueryInterface|Serializable|array|stdClass ...$expression
     */
    public static function or(Document|Serializable|QueryInterface|stdClass|array ...$expression): OrQuery
    {
        return new OrQuery(...$expression);
    }

    /**
     * Specifies a polygon to using legacy coordinate pairs for $geoWithin queries. The 2d index supports $center.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/polygon/
     * @param BSONArray|PackedArray|array $points
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
     * @param int $value
     */
    public static function size(int $value): SizeQuery
    {
        return new SizeQuery($value);
    }

    /**
     * Limits the number of elements projected from an array. Supports skip and limit slices.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/slice/
     * @param int $limit
     * @param int $skip
     */
    public static function slice(int $limit, int $skip): SliceQuery
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
     * @param BSONArray|PackedArray|array|int|non-empty-string $type
     */
    public static function type(PackedArray|BSONArray|array|int|string $type): TypeQuery
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
