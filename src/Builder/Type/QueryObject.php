<?php

namespace MongoDB\Builder\Type;

use MongoDB\BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_is_list;
use function count;
use function is_array;
use function is_numeric;
use function sprintf;

/**
 * Helper class to validate query objects.
 *
 * Queries are a mix of query operator ($or, $and, $nor...) and field path to filter operators ($eq, $gt, $in...).
 *
 * @internal
 */
final class QueryObject implements QueryInterface
{
    public array $queries = [];

    public static function create(QueryInterface|Serializable|array|stdClass $queries): QueryInterface
    {
        if ($queries instanceof QueryInterface) {
            return $queries;
        }

        return new self($queries);
    }

    private function __construct(
        Serializable|array|stdClass $queries,
    ) {
        $seenQueryOperators = [];
        foreach ($queries as $fieldPath => $query) {
            if ($query instanceof QueryInterface) {
                if (! is_numeric($fieldPath)) {
                    throw new InvalidArgumentException(sprintf('Query operator "%s" cannot be used with a field path. Got "%s".', $query::NAME, $fieldPath));
                }

                if (! $query instanceof self) {
                    if (isset($seenQueryOperators[$query::NAME])) {
                        throw new InvalidArgumentException(sprintf('Query operator "%s" cannot be used multiple times in the same query.', $query::NAME));
                    }

                    $seenQueryOperators[$query::NAME] = true;
                }

                $this->queries[] = $query;
                continue;
            }

            // Convert list of filters into $and
            if (self::isListOfFilters($query)) {
                if (count($query) === 1) {
                    $query = $query[0];
                } else {
                    $query = new CombinedQueryFilter($query);
                }
            }

            // Numbers are valid field paths, nothing to validate.
            $this->queries[$fieldPath] = $query;
        }
    }

    private static function isListOfFilters(mixed $values): bool
    {
        if (! is_array($values) || ! array_is_list($values)) {
            return false;
        }

        foreach ($values as $value) {
            if ($value instanceof QueryFilterInterface) {
                return true;
            }
        }

        return false;
    }
}
