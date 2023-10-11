<?php

namespace MongoDB\Builder\Type;

use InvalidArgumentException;
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use stdClass;

use function get_debug_type;
use function is_array;
use function sprintf;

/**
 * List of filters that apply to the same field path.
 */
class CombinedQueryFilter implements QueryFilterInterface
{
    /** @param list<QueryFilterInterface|Serializable|array|stdClass> $filters */
    public function __construct(public array $filters)
    {
        foreach ($filters as $filter) {
            if (! $filter instanceof QueryFilterInterface && ! $filter instanceof Serializable && ! is_array($filter) && ! $filter instanceof stdClass) {
                throw new InvalidArgumentException(sprintf('Expected filters to be a list of %s, %s, array or stdClass, %s given.', QueryFilterInterface::class, Document::class, get_debug_type($filter)));
            }
        }
    }
}
