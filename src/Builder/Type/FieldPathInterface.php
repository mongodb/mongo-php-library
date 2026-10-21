<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

/**
 * Aggregation expressions use field path to access fields in the input documents.
 *
 * @see https://www.mongodb.com/docs/manual/meta/aggregation-quick-reference/#field-paths
 *
 * @property-read string $path The field path string, without the leading "$" character.
 */
interface FieldPathInterface extends ExpressionInterface
{
}
