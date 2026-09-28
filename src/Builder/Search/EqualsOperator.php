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
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * The equals operator checks whether a field matches a value you specify.
 *
 * New in MongoDB 5.0
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/
 * @internal
 */
final class EqualsOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'equals';
    public const PROPERTIES = ['path' => 'path', 'value' => 'value', 'score' => 'score', 'doesNotAffect' => 'doesNotAffect'];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var Binary|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|bool|float|int|null|string $value */
    public readonly DateTimeInterface|Binary|Decimal128|Int64|ObjectId|UTCDateTime|bool|float|int|null|string $value;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /** @var Optional|BSONArray|PackedArray|array|string $doesNotAffect */
    public readonly Optional|PackedArray|BSONArray|array|string $doesNotAffect;

    /**
     * @param array|string $path
     * @param Binary|DateTimeInterface|Decimal128|Int64|ObjectId|UTCDateTime|bool|float|int|null|string $value
     * @param Optional|Document|Serializable|array|stdClass $score
     * @param Optional|BSONArray|PackedArray|array|string $doesNotAffect
     */
    public function __construct(
        array|string $path,
        DateTimeInterface|Binary|Decimal128|Int64|ObjectId|UTCDateTime|bool|float|int|null|string $value,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
        Optional|PackedArray|BSONArray|array|string $doesNotAffect = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->value = $value;
        $this->score = $score;
        if (is_array($doesNotAffect) && ! array_is_list($doesNotAffect)) {
            throw new InvalidArgumentException('Expected $doesNotAffect argument to be a list, got an associative array.');
        }

        $this->doesNotAffect = $doesNotAffect;
    }
}
