<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Removes or excludes fields from documents.
 * Alias for $project stage that removes or excludes fields.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/
 */
class UnsetStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var list<FieldPath|non-empty-string> $field */
    public readonly array $field;

    /**
     * @param FieldPath|non-empty-string ...$field
     * @no-named-arguments
     */
    public function __construct(FieldPath|string ...$field)
    {
        if (\count($field) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $field, got %d.', 1, \count($field)));
        }

        if (! array_is_list($field)) {
            throw new InvalidArgumentException('Expected $field arguments to be a list (array), named arguments are not supported');
        }

        $this->field = $field;
    }

    public function getOperator(): string
    {
        return '$unset';
    }
}
