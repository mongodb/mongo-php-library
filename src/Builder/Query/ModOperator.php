<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Performs a modulo operation on the value of a field and selects documents with a specified result.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/
 * @internal
 */
final class ModOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$mod';
    public const PROPERTIES = ['divisor' => 'divisor', 'remainder' => 'remainder'];

    /** @var Decimal128|Int64|float|int $divisor */
    public readonly Decimal128|Int64|float|int $divisor;

    /** @var Decimal128|Int64|float|int $remainder */
    public readonly Decimal128|Int64|float|int $remainder;

    /**
     * @param Decimal128|Int64|float|int $divisor
     * @param Decimal128|Int64|float|int $remainder
     */
    public function __construct(Decimal128|Int64|float|int $divisor, Decimal128|Int64|float|int $remainder)
    {
        $this->divisor = $divisor;
        $this->remainder = $remainder;
    }
}
