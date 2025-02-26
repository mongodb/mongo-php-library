<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Matches values that are equal to a specified value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/
 * @internal
 */
final class EqOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$eq';
    public const PROPERTIES = ['value' => 'value'];

    /** @var DateTimeInterface|Type|array|bool|float|int|null|stdClass|string $value */
    public readonly DateTimeInterface|Type|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string $value
     */
    public function __construct(DateTimeInterface|Type|stdClass|array|bool|float|int|null|string $value)
    {
        $this->value = $value;
    }
}
