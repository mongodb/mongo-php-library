<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

/**
 * Validate documents against the given JSON Schema.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/jsonSchema/
 * @internal
 */
final class JsonSchemaOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$jsonSchema';
    public const PROPERTIES = ['schema' => 'schema'];

    /** @var Document|Serializable|array|stdClass $schema */
    public readonly Document|Serializable|stdClass|array $schema;

    /**
     * @param Document|Serializable|array|stdClass $schema
     */
    public function __construct(Document|Serializable|stdClass|array $schema)
    {
        $this->schema = $schema;
    }
}
