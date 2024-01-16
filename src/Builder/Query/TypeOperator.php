<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Selects documents if a field is of the specified type.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/
 */
class TypeOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var list<int|non-empty-string> $type */
    public readonly array $type;

    /**
     * @param int|non-empty-string ...$type
     * @no-named-arguments
     */
    public function __construct(int|string ...$type)
    {
        if (\count($type) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $type, got %d.', 1, \count($type)));
        }

        if (! array_is_list($type)) {
            throw new InvalidArgumentException('Expected $type arguments to be a list (array), named arguments are not supported');
        }

        $this->type = $type;
    }

    public function getOperator(): string
    {
        return '$type';
    }
}
