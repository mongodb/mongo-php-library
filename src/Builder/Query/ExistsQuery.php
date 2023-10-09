<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Matches documents that have the specified field.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/exists/
 */
class ExistsQuery implements QueryInterface
{
    public const NAME = '$exists';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param bool $exists */
    public bool $exists;

    /**
     * @param bool $exists
     */
    public function __construct(bool $exists)
    {
        $this->exists = $exists;
    }
}
