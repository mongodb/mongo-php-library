<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Selects documents if the array field is a specified size.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/size/
 * @internal
 */
final class SizeOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$size';
    public const PROPERTIES = ['value' => 'value'];

    /** @var int $value */
    public readonly int $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }
}
