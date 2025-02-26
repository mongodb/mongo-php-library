<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use DateTimeInterface;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use stdClass;

/**
 * The range operator supports querying and scoring numeric, date, and string values.
 * You can use this operator to find results that are within a given numeric, date, objectId, or letter (from the English alphabet) range.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/range/
 * @internal
 */
final class RangeOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'range';

    public const PROPERTIES = [
        'path' => 'path',
        'gt' => 'gt',
        'gte' => 'gte',
        'lt' => 'lt',
        'lte' => 'lte',
        'score' => 'score',
    ];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gt */
    public readonly Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gt;

    /** @var Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gte */
    public readonly Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gte;

    /** @var Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lt */
    public readonly Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lt;

    /** @var Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lte */
    public readonly Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lte;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gt
     * @param Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gte
     * @param Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lt
     * @param Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lte
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gt = Optional::Undefined,
        Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $gte = Optional::Undefined,
        Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lt = Optional::Undefined,
        Optional|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|float|int|string $lte = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->gt = $gt;
        $this->gte = $gte;
        $this->lt = $lt;
        $this->lte = $lte;
        $this->score = $score;
    }
}
