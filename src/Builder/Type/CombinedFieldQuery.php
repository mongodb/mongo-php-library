<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function get_debug_type;
use function is_array;
use function sprintf;

/**
 * List of filters that apply to the same field path.
 */
class CombinedFieldQuery implements FieldQueryInterface
{
    public function __construct(
        /** @var list<FieldQueryInterface|Serializable|array|stdClass> $fieldQueries */
        public readonly array $fieldQueries,
    ) {
        foreach ($fieldQueries as $fieldQuery) {
            if (! $fieldQuery instanceof FieldQueryInterface && ! $fieldQuery instanceof Serializable && ! is_array($fieldQuery) && ! $fieldQuery instanceof stdClass) {
                throw new InvalidArgumentException(sprintf('Expected filters to be a list of %s, %s, array or stdClass, %s given.', FieldQueryInterface::class, Document::class, get_debug_type($fieldQuery)));
            }
        }
    }
}
