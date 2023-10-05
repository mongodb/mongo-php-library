<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use stdClass;

/**
 * Joins query clauses with a logical OR returns all documents that match the conditions of either clause.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/or/
 */
class OrQuery implements QueryInterface
{
    public const NAME = '$or';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @no-named-arguments
     * @param list<QueryInterface|array|stdClass> ...$expression
     */
    public array $expression;

    /**
     * @param QueryInterface|array|stdClass ...$expression
     */
    public function __construct(QueryInterface|stdClass|array ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of QueryInterface|array|stdClass, named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
