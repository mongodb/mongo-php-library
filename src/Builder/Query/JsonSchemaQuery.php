<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use stdClass;

class JsonSchemaQuery implements QueryInterface
{
    public const NAME = '$jsonSchema';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|Serializable|array|stdClass $schema */
    public Document|Serializable|stdClass|array $schema;

    /**
     * @param Document|Serializable|array|stdClass $schema
     */
    public function __construct(Document|Serializable|stdClass|array $schema)
    {
        $this->schema = $schema;
    }
}
