<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Returns a document containing the constituent parts of a date.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToParts/
 * @internal
 */
final class DateToPartsOperator implements ResolvesToObject, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$dateToParts';
    public const PROPERTIES = ['date' => 'date', 'timezone' => 'timezone', 'iso8601' => 'iso8601'];

    /** @var DateTimeInterface|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int|string $date The input date for which to return parts. date can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public readonly DateTimeInterface|ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int|string $date;

    /** @var Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public readonly Optional|ResolvesToString|string $timezone;

    /** @var Optional|bool $iso8601 If set to true, modifies the output document to use ISO week date fields. Defaults to false. */
    public readonly Optional|bool $iso8601;

    /**
     * @param DateTimeInterface|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int|string $date The input date for which to return parts. date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|bool $iso8601 If set to true, modifies the output document to use ISO week date fields. Defaults to false.
     */
    public function __construct(
        DateTimeInterface|ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int|string $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|bool $iso8601 = Optional::Undefined,
    ) {
        if (is_string($date) && ! str_starts_with($date, '$')) {
            throw new InvalidArgumentException('Argument $date can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->date = $date;
        $this->timezone = $timezone;
        $this->iso8601 = $iso8601;
    }
}
