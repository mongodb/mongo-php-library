<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Projects the available per-document metadata.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/meta/
 */
class MetaQuery implements ExpressionInterface
{
    public const NAME = '$meta';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    public function __construct()
    {
    }
}
