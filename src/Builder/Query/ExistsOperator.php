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
 * Matches documents that have the specified field.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/exists/
 * @internal
 */
final class ExistsOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$exists';
    public const PROPERTIES = ['exists' => 'exists'];

    /** @var bool $exists */
    public readonly bool $exists;

    /**
     * @param bool $exists
     */
    public function __construct(bool $exists = true)
    {
        $this->exists = $exists;
    }
}
