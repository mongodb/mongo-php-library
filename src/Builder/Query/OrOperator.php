<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Joins query clauses with a logical OR returns all documents that match the conditions of either clause.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/or/
 */
class OrOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var list<QueryInterface|array> $queries */
    public readonly array $queries;

    /**
     * @param QueryInterface|array ...$queries
     * @no-named-arguments
     */
    public function __construct(QueryInterface|array ...$queries)
    {
        if (\count($queries) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $queries, got %d.', 1, \count($queries)));
        }

        if (! array_is_list($queries)) {
            throw new InvalidArgumentException('Expected $queries arguments to be a list (array), named arguments are not supported');
        }

        $this->queries = $queries;
    }

    public function getOperator(): string
    {
        return '$or';
    }
}
