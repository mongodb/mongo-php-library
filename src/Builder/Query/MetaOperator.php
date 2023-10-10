<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\ProjectionInterface;

/**
 * Projects the available per-document metadata.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/meta/
 */
class MetaOperator implements ProjectionInterface
{
    public const NAME = '$meta';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    public function __construct()
    {
    }
}
