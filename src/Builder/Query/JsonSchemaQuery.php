<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;

class JsonSchemaQuery implements QueryInterface
{
    public const NAME = '$jsonSchema';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|Serializable|array|object $schema */
    public array|object $schema;

    /**
     * @param Document|Serializable|array|object $schema
     */
    public function __construct(array|object $schema)
    {
        $this->schema = $schema;
    }
}
