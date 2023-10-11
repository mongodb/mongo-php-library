<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

/**
 * Validate documents against the given JSON Schema.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/jsonSchema/
 */
class JsonSchemaOperator implements QueryInterface
{
    public const NAME = '$jsonSchema';
    public const ENCODE = Encode::Single;

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
