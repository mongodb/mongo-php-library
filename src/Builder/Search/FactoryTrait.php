<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use DateTimeInterface;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * @internal
 */
trait FactoryTrait
{
    /**
     * The autocomplete operator performs a search for a word or phrase that
     * contains a sequence of characters from an incomplete input string. The
     * fields that you intend to query with the autocomplete operator must be
     * indexed with the autocomplete data type in the collection's index definition.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/
     * @param array|string $path
     * @param string $query
     * @param Optional|string $tokenOrder
     * @param Optional|Document|Serializable|array|stdClass $fuzzy
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function autocomplete(
        array|string $path,
        string $query,
        Optional|string $tokenOrder = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $fuzzy = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): AutocompleteOperator {
        return new AutocompleteOperator($path, $query, $tokenOrder, $fuzzy, $score);
    }

    /**
     * The compound operator combines two or more operators into a single query.
     * Each element of a compound query is called a clause, and each clause
     * consists of one or more sub-queries.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/compound/
     * @param Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $must
     * @param Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $mustNot
     * @param Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $should
     * @param Optional|BSONArray|Document|PackedArray|SearchOperatorInterface|Serializable|array|stdClass $filter
     * @param Optional|int $minimumShouldMatch
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function compound(
        Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $must = Optional::Undefined,
        Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $mustNot = Optional::Undefined,
        Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $should = Optional::Undefined,
        Optional|Document|PackedArray|Serializable|SearchOperatorInterface|BSONArray|stdClass|array $filter = Optional::Undefined,
        Optional|int $minimumShouldMatch = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): CompoundOperator {
        return new CompoundOperator($must, $mustNot, $should, $filter, $minimumShouldMatch, $score);
    }

    /**
     * The embeddedDocument operator is similar to $elemMatch operator.
     * It constrains multiple query predicates to be satisfied from a single
     * element of an array of embedded documents. embeddedDocument can be used only
     * for queries over fields of the embeddedDocuments
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/embedded-document/
     * @param array|string $path
     * @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function embeddedDocument(
        array|string $path,
        Document|Serializable|SearchOperatorInterface|stdClass|array $operator,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): EmbeddedDocumentOperator {
        return new EmbeddedDocumentOperator($path, $operator, $score);
    }

    /**
     * The equals operator checks whether a field matches a value you specify.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/
     * @param array|string $path
     * @param Binary|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|bool|float|int|null|string $value
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function equals(
        array|string $path,
        DateTimeInterface|Binary|Decimal128|Int64|ObjectId|UTCDateTime|bool|float|int|null|string $value,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): EqualsOperator {
        return new EqualsOperator($path, $value, $score);
    }

    /**
     * The exists operator tests if a path to a specified indexed field name exists in a document.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/exists/
     * @param array|string $path
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function exists(
        array|string $path,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): ExistsOperator {
        return new ExistsOperator($path, $score);
    }

    /**
     * The facet collector groups results by values or ranges in the specified
     * faceted fields and returns the count for each of those groups.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/facet/
     * @param Document|Serializable|array|stdClass $facets
     * @param Optional|Document|SearchOperatorInterface|Serializable|array|stdClass $operator
     */
    public static function facet(
        Document|Serializable|stdClass|array $facets,
        Optional|Document|Serializable|SearchOperatorInterface|stdClass|array $operator = Optional::Undefined,
    ): FacetOperator {
        return new FacetOperator($facets, $operator);
    }

    /**
     * The geoShape operator supports querying shapes with a relation to a given
     * geometry if indexShapes is set to true in the index definition.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/geoShape/
     * @param array|string $path
     * @param string $relation
     * @param Document|GeometryInterface|Serializable|array|stdClass $geometry
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function geoShape(
        array|string $path,
        string $relation,
        Document|Serializable|GeometryInterface|stdClass|array $geometry,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): GeoShapeOperator {
        return new GeoShapeOperator($path, $relation, $geometry, $score);
    }

    /**
     * The geoWithin operator supports querying geographic points within a given
     * geometry. Only points are returned, even if indexShapes value is true in
     * the index definition.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/geoWithin/
     * @param array|string $path
     * @param Optional|Document|Serializable|array|stdClass $box
     * @param Optional|Document|Serializable|array|stdClass $circle
     * @param Optional|Document|GeometryInterface|Serializable|array|stdClass $geometry
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function geoWithin(
        array|string $path,
        Optional|Document|Serializable|stdClass|array $box = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $circle = Optional::Undefined,
        Optional|Document|Serializable|GeometryInterface|stdClass|array $geometry = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): GeoWithinOperator {
        return new GeoWithinOperator($path, $box, $circle, $geometry, $score);
    }

    /**
     * The in operator performs a search for an array of BSON values in a field.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/in/
     * @param array|string $path
     * @param BSONArray|DateTimeInterface|PackedArray|Type|array|bool|float|int|null|stdClass|string $value
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function in(
        array|string $path,
        DateTimeInterface|PackedArray|Type|BSONArray|stdClass|array|bool|float|int|null|string $value,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): InOperator {
        return new InOperator($path, $value, $score);
    }

    /**
     * The moreLikeThis operator returns documents similar to input documents.
     * The moreLikeThis operator allows you to build features for your applications
     * that display similar or alternative results based on one or more given documents.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/moreLikeThis/
     * @param BSONArray|Document|PackedArray|Serializable|array|stdClass $like
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function moreLikeThis(
        Document|PackedArray|Serializable|BSONArray|stdClass|array $like,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): MoreLikeThisOperator {
        return new MoreLikeThisOperator($like, $score);
    }

    /**
     * The near operator supports querying and scoring numeric, date, and GeoJSON point values.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/near/
     * @param array|string $path
     * @param DateTimeInterface|Decimal128|Document|GeometryInterface|Int64|Serializable|UTCDateTime|array|float|int|stdClass $origin
     * @param Decimal128|Int64|float|int $pivot
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function near(
        array|string $path,
        DateTimeInterface|Decimal128|Document|Int64|Serializable|UTCDateTime|GeometryInterface|stdClass|array|float|int $origin,
        Decimal128|Int64|float|int $pivot,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): NearOperator {
        return new NearOperator($path, $origin, $pivot, $score);
    }

    /**
     * The phrase operator performs search for documents containing an ordered sequence of terms using the analyzer specified in the index configuration.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/phrase/
     * @param array|string $path
     * @param BSONArray|PackedArray|array|string $query
     * @param Optional|int $slop
     * @param Optional|string $synonyms
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function phrase(
        array|string $path,
        PackedArray|BSONArray|array|string $query,
        Optional|int $slop = Optional::Undefined,
        Optional|string $synonyms = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): PhraseOperator {
        return new PhraseOperator($path, $query, $slop, $synonyms, $score);
    }

    /**
     * @see https://www.mongodb.com/docs/atlas/atlas-search/queryString/
     * @param array|string $defaultPath
     * @param string $query
     */
    public static function queryString(array|string $defaultPath, string $query): QueryStringOperator
    {
        return new QueryStringOperator($defaultPath, $query);
    }

    /**
     * The range operator supports querying and scoring numeric, date, and string values.
     * You can use this operator to find results that are within a given numeric, date, objectId, or letter (from the English alphabet) range.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/range/
     * @param array|string $path
     * @param Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gt
     * @param Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gte
     * @param Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lt
     * @param Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lte
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function range(
        array|string $path,
        Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gt = Optional::Undefined,
        Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gte = Optional::Undefined,
        Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lt = Optional::Undefined,
        Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lte = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): RangeOperator {
        return new RangeOperator($path, $gt, $gte, $lt, $lte, $score);
    }

    /**
     * regex interprets the query field as a regular expression.
     * regex is a term-level operator, meaning that the query field isn't analyzed.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/regex/
     * @param array|string $path
     * @param string $query
     * @param Optional|bool $allowAnalyzedField
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function regex(
        array|string $path,
        string $query,
        Optional|bool $allowAnalyzedField = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): RegexOperator {
        return new RegexOperator($path, $query, $allowAnalyzedField, $score);
    }

    /**
     * The text operator performs a full-text search using the analyzer that you specify in the index configuration.
     * If you omit an analyzer, the text operator uses the default standard analyzer.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/
     * @param array|string $path
     * @param string $query
     * @param Optional|Document|Serializable|array|stdClass $fuzzy
     * @param Optional|string $matchCriteria
     * @param Optional|string $synonyms
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function text(
        array|string $path,
        string $query,
        Optional|Document|Serializable|stdClass|array $fuzzy = Optional::Undefined,
        Optional|string $matchCriteria = Optional::Undefined,
        Optional|string $synonyms = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): TextOperator {
        return new TextOperator($path, $query, $fuzzy, $matchCriteria, $synonyms, $score);
    }

    /**
     * The wildcard operator enables queries which use special characters in the search string that can match any character.
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/wildcard/
     * @param array|string $path
     * @param string $query
     * @param Optional|bool $allowAnalyzedField
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public static function wildcard(
        array|string $path,
        string $query,
        Optional|bool $allowAnalyzedField = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ): WildcardOperator {
        return new WildcardOperator($path, $query, $allowAnalyzedField, $score);
    }
}
