<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\FieldPath;

/**
 * Removes or excludes fields from documents.
 * Alias for $project stage that removes or excludes fields.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/
 */
class UnsetStage implements StageInterface
{
    public const NAME = '$unset';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @no-named-arguments
     * @param list<FieldPath|non-empty-string> ...$field
     */
    public array $field;

    /**
     * @param FieldPath|non-empty-string ...$field
     */
    public function __construct(FieldPath|string ...$field)
    {
        if (\count($field) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $field, got %d.', 1, \count($field)));
        }
        if (! \array_is_list($field)) {
            throw new \InvalidArgumentException('Expected $field arguments to be a list of FieldPath|non-empty-string, named arguments are not supported');
        }
        $this->field = $field;
    }
}
