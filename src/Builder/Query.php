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

final class Query
{
    /**
     * @param mixed ...$value
     */
    public static function all(mixed ...$value): AllQuery
    {
        return new AllQuery(...$value);
    }

    /**
     * @param QueryInterface|array|object ...$expression
     */
    public static function and(array|object ...$expression): AndQuery
    {
        return new AndQuery(...$expression);
    }

    /**
     * @param BSONArray|Binary|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $bitmask
     */
    public static function bitsAllClear(
        Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask,
    ): BitsAllClearQuery
    {
        return new BitsAllClearQuery($bitmask);
    }

    /**
     * @param BSONArray|Binary|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $bitmask
     */
    public static function bitsAllSet(Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask): BitsAllSetQuery
    {
        return new BitsAllSetQuery($bitmask);
    }

    /**
     * @param BSONArray|Binary|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $bitmask
     */
    public static function bitsAnyClear(
        Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask,
    ): BitsAnyClearQuery
    {
        return new BitsAnyClearQuery($bitmask);
    }

    /**
     * @param BSONArray|Binary|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $bitmask
     */
    public static function bitsAnySet(Binary|Int64|PackedArray|BSONArray|array|int|string $bitmask): BitsAnySetQuery
    {
        return new BitsAnySetQuery($bitmask);
    }

    /**
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $value
     */
    public static function box(PackedArray|BSONArray|array $value): BoxQuery
    {
        return new BoxQuery($value);
    }

    /**
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $value
     */
    public static function center(PackedArray|BSONArray|array $value): CenterQuery
    {
        return new CenterQuery($value);
    }

    /**
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $value
     */
    public static function centerSphere(PackedArray|BSONArray|array $value): CenterSphereQuery
    {
        return new CenterSphereQuery($value);
    }

    /**
     * @param non-empty-string $comment
     */
    public static function comment(string $comment): CommentQuery
    {
        return new CommentQuery($comment);
    }

    /**
     * @param Document|Serializable|array|object $queries
     */
    public static function elemMatch(array|object $queries): ElemMatchQuery
    {
        return new ElemMatchQuery($queries);
    }

    /**
     * @param mixed $value
     */
    public static function eq(mixed $value): EqQuery
    {
        return new EqQuery($value);
    }

    /**
     * @param bool $exists
     */
    public static function exists(bool $exists): ExistsQuery
    {
        return new ExistsQuery($exists);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function expr(mixed $expression): ExprQuery
    {
        return new ExprQuery($expression);
    }

    /**
     * @param array|object $geometry
     */
    public static function geoIntersects(array|object $geometry): GeoIntersectsQuery
    {
        return new GeoIntersectsQuery($geometry);
    }

    /**
     * @param array|object $geometry
     */
    public static function geoWithin(array|object $geometry): GeoWithinQuery
    {
        return new GeoWithinQuery($geometry);
    }

    /**
     * @param non-empty-string $type
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $coordinates
     * @param Document|Serializable|array|object $crs
     */
    public static function geometry(
        string $type,
        PackedArray|BSONArray|array $coordinates,
        array|object $crs,
    ): GeometryQuery
    {
        return new GeometryQuery($type, $coordinates, $crs);
    }

    /**
     * @param mixed $value
     */
    public static function gt(mixed $value): GtQuery
    {
        return new GtQuery($value);
    }

    /**
     * @param mixed $value
     */
    public static function gte(mixed $value): GteQuery
    {
        return new GteQuery($value);
    }

    /**
     * @param mixed $value
     */
    public static function in(mixed $value): InQuery
    {
        return new InQuery($value);
    }

    /**
     * @param Document|Serializable|array|object $schema
     */
    public static function jsonSchema(array|object $schema): JsonSchemaQuery
    {
        return new JsonSchemaQuery($schema);
    }

    /**
     * @param mixed $value
     */
    public static function lt(mixed $value): LtQuery
    {
        return new LtQuery($value);
    }

    /**
     * @param mixed $value
     */
    public static function lte(mixed $value): LteQuery
    {
        return new LteQuery($value);
    }

    /**
     * @param Int64|float|int $value
     */
    public static function maxDistance(Int64|float|int $value): MaxDistanceQuery
    {
        return new MaxDistanceQuery($value);
    }

    public static function meta(): MetaQuery
    {
        return new MetaQuery();
    }

    /**
     * @param Int64|float|int $value
     */
    public static function minDistance(Int64|float|int $value): MinDistanceQuery
    {
        return new MinDistanceQuery($value);
    }

    /**
     * @param Int64|int $divisor
     * @param Int64|int $remainder
     */
    public static function mod(Int64|int $divisor, Int64|int $remainder): ModQuery
    {
        return new ModQuery($divisor, $remainder);
    }

    public static function natural(): NaturalQuery
    {
        return new NaturalQuery();
    }

    /**
     * @param mixed $value
     */
    public static function ne(mixed $value): NeQuery
    {
        return new NeQuery($value);
    }

    /**
     * @param array|object $geometry
     * @param Int64|Optional|int $maxDistance Distance in meters. Limits the results to those documents that are at most the specified distance from the center point.
     * @param Int64|Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public static function near(
        array|object $geometry,
        Int64|Optional|int $maxDistance = Optional::Undefined,
        Int64|Optional|int $minDistance = Optional::Undefined,
    ): NearQuery
    {
        return new NearQuery($geometry, $maxDistance, $minDistance);
    }

    /**
     * @param array|object $geometry
     * @param Int64|Optional|int $maxDistance Distance in meters.
     * @param Int64|Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public static function nearSphere(
        array|object $geometry,
        Int64|Optional|int $maxDistance = Optional::Undefined,
        Int64|Optional|int $minDistance = Optional::Undefined,
    ): NearSphereQuery
    {
        return new NearSphereQuery($geometry, $maxDistance, $minDistance);
    }

    /**
     * @param mixed $value
     */
    public static function nin(mixed $value): NinQuery
    {
        return new NinQuery($value);
    }

    /**
     * @param QueryInterface|array|object ...$expression
     */
    public static function nor(array|object ...$expression): NorQuery
    {
        return new NorQuery(...$expression);
    }

    /**
     * @param QueryInterface|array|object $expression
     */
    public static function not(array|object $expression): NotQuery
    {
        return new NotQuery($expression);
    }

    /**
     * @param QueryInterface|array|object ...$expression
     */
    public static function or(array|object ...$expression): OrQuery
    {
        return new OrQuery(...$expression);
    }

    /**
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $points
     */
    public static function polygon(PackedArray|BSONArray|array $points): PolygonQuery
    {
        return new PolygonQuery($points);
    }

    public static function rand(): RandQuery
    {
        return new RandQuery();
    }

    /**
     * @param Regex $regex
     */
    public static function regex(Regex $regex): RegexQuery
    {
        return new RegexQuery($regex);
    }

    /**
     * @param Int64|int $value
     */
    public static function size(Int64|int $value): SizeQuery
    {
        return new SizeQuery($value);
    }

    /**
     * @param Int64|int $limit
     * @param Int64|int $skip
     */
    public static function slice(Int64|int $limit, Int64|int $skip): SliceQuery
    {
        return new SliceQuery($limit, $skip);
    }

    /**
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
     * @param BSONArray|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $type
     */
    public static function type(Int64|PackedArray|BSONArray|array|int|string $type): TypeQuery
    {
        return new TypeQuery($type);
    }

    /**
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
