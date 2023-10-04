<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;

class ElemMatchQuery implements QueryInterface
{
    public const NAME = '$elemMatch';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Document|Serializable|array|object $queries */
    public array|object $queries;

    /**
     * @param Document|Serializable|array|object $queries
     */
    public function __construct(array|object $queries)
    {
        $this->queries = $queries;
    }
}
