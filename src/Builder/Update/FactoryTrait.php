<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Update;

use DateTimeInterface;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * @internal
 */
trait FactoryTrait
{
    /**
     * Adds a value to an array unless the value is already present.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/addToSet/
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public static function addToSet(
        DateTimeInterface|Type|stdClass|array|bool|float|int|null|string ...$field,
    ): AddToSetOperator {
        return new AddToSetOperator(...$field);
    }

    /**
     * Performs bitwise updates of integer values.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/bit/
     * @param Document|Serializable|array|stdClass ...$field Each field maps to an object containing exactly one bitwise operation:
     * and, or, or xor.
     */
    public static function bit(Document|Serializable|stdClass|array ...$field): BitOperator
    {
        return new BitOperator(...$field);
    }

    /**
     * Sets the value of a field to the current date as either a Date or Timestamp.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/currentDate/
     * @param Document|Serializable|array|bool|stdClass ...$field The value for each field can be either:
     * - true, to set the field to the current date.
     * - a document with $type set to "date" or "timestamp".
     */
    public static function currentDate(Document|Serializable|stdClass|array|bool ...$field): CurrentDateOperator
    {
        return new CurrentDateOperator(...$field);
    }

    /**
     * Increments a field by the specified numeric amount.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/inc/
     * @param Decimal128|Int64|float|int ...$field
     */
    public static function inc(Decimal128|Int64|float|int ...$field): IncOperator
    {
        return new IncOperator(...$field);
    }

    /**
     * Updates a field only if the specified value is greater than the current field value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/max/
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public static function max(
        DateTimeInterface|Type|stdClass|array|bool|float|int|null|string ...$field,
    ): MaxOperator {
        return new MaxOperator(...$field);
    }

    /**
     * Updates a field only if the specified value is less than the current field value.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/min/
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public static function min(
        DateTimeInterface|Type|stdClass|array|bool|float|int|null|string ...$field,
    ): MinOperator {
        return new MinOperator(...$field);
    }

    /**
     * Multiplies the value of a field by the specified number.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/mul/
     * @param Decimal128|Int64|float|int ...$field
     */
    public static function mul(Decimal128|Int64|float|int ...$field): MulOperator
    {
        return new MulOperator(...$field);
    }

    /**
     * Removes the first or last element of an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pop/
     * @param int ...$field
     */
    public static function pop(int ...$field): PopOperator
    {
        return new PopOperator(...$field);
    }

    /**
     * Removes all array elements that match a specified value or condition.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pull/
     * @param DateTimeInterface|FieldQueryInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public static function pull(
        DateTimeInterface|Type|FieldQueryInterface|stdClass|array|bool|float|int|null|string ...$field,
    ): PullOperator {
        return new PullOperator(...$field);
    }

    /**
     * Removes all matching values from an array.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pullAll/
     * @param BSONArray|PackedArray|array ...$field
     */
    public static function pullAll(PackedArray|BSONArray|array ...$field): PullAllOperator
    {
        return new PullAllOperator(...$field);
    }

    /**
     * Appends a specified value to an array, with optional modifiers.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/push/
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public static function push(
        DateTimeInterface|Type|stdClass|array|bool|float|int|null|string ...$field,
    ): PushOperator {
        return new PushOperator(...$field);
    }

    /**
     * Renames a field.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/rename/
     * @param string ...$field
     */
    public static function rename(string ...$field): RenameOperator
    {
        return new RenameOperator(...$field);
    }

    /**
     * Sets the value of a field.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/set/
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public static function set(
        DateTimeInterface|Type|stdClass|array|bool|float|int|null|string ...$field,
    ): SetOperator {
        return new SetOperator(...$field);
    }

    /**
     * Sets the value of a field if an update with upsert creates a new document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/setOnInsert/
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public static function setOnInsert(
        DateTimeInterface|Type|stdClass|array|bool|float|int|null|string ...$field,
    ): SetOnInsertOperator {
        return new SetOnInsertOperator(...$field);
    }

    /**
     * Removes the specified field from a document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/unset/
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string ...$field
     */
    public static function unset(
        DateTimeInterface|Type|stdClass|array|bool|float|int|null|string ...$field,
    ): UnsetOperator {
        return new UnsetOperator(...$field);
    }
}
