<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_any;
use function array_is_list;
use function array_key_first;
use function count;
use function get_debug_type;
use function is_array;
use function is_int;
use function sprintf;
use function str_starts_with;

/**
 * Helper class to validate query objects.
 *
 * Queries are a mix of query operators ($or, $and, $nor, $jsonSchema...) and field query operators ($eq, $gt, $in...)
 * associated to a field path.
 */
final class QueryObject implements QueryInterface
{
    public readonly array $queries;

    /** @param array<DateTimeInterface|QueryInterface|FieldQueryInterface|Type|stdClass|array|bool|float|int|string|null> $queries */
    public static function create(array $queries): QueryInterface
    {
        // We don't wrap a single query in a QueryObject
        if (count($queries) === 1 && isset($queries[0]) && $queries[0] instanceof QueryInterface) {
            return $queries[0];
        }

        return new self($queries);
    }

    /** @param array<DateTimeInterface|QueryInterface|FieldQueryInterface|Type|stdClass|array|bool|float|int|string|null> $queriesOrArrayOfQueries */
    private function __construct(array $queriesOrArrayOfQueries, bool $isFieldMap = false)
    {
        // If the first element is an array and not an operator, we assume variadic arguments were not used.
        // An empty array is unwrapped as well, so that it results in an empty query instead of a "0" field.
        if (
            ! $isFieldMap &&
            count($queriesOrArrayOfQueries) === 1 &&
            isset($queriesOrArrayOfQueries[0]) &&
            is_array($queriesOrArrayOfQueries[0]) &&
            (count($queriesOrArrayOfQueries[0]) === 0 || ! str_starts_with((string) array_key_first($queriesOrArrayOfQueries[0]), '$'))
        ) {
            $queriesOrArrayOfQueries = $queriesOrArrayOfQueries[0];
            $isFieldMap = true;
        }

        // Integer keys produced by variadic arguments are positions, not field names
        $isFieldMap = $isFieldMap || ! self::hasPositionalKeys($queriesOrArrayOfQueries);

        $seenQueryOperators = [];
        $queries = [];

        foreach ($queriesOrArrayOfQueries as $fieldPath => $query) {
            if ($query instanceof QueryInterface) {
                if ($query instanceof OperatorInterface) {
                    if (isset($seenQueryOperators[$query::NAME])) {
                        throw new InvalidArgumentException(sprintf('Query operator "%s" cannot be used multiple times in the same query.', $query::NAME));
                    }

                    $seenQueryOperators[$query::NAME] = true;
                }

                $queries[] = $query;
                continue;
            }

            // A positional argument that is not a query operator is a query document, its fields are merged
            if (! $isFieldMap && is_int($fieldPath)) {
                if (! is_array($query) && ! $query instanceof stdClass) {
                    throw new InvalidArgumentException(sprintf('Expected query to be a query operator, an array or a stdClass, %s given', get_debug_type($query)));
                }

                /** @var array<DateTimeInterface|QueryInterface|FieldQueryInterface|Type|stdClass|array|bool|float|int|string|null> $fields */
                $fields = (array) $query;
                $queries[] = new self($fields, true);
                continue;
            }

            // Convert list of filters into CombinedFieldQuery
            if (self::isListOfFilters($query)) {
                if (count($query) === 1) {
                    $query = $query[0];
                } else {
                    $query = new CombinedFieldQuery($query);
                }
            }

            $queries[$fieldPath] = $query;
        }

        $this->queries = $queries;
    }

    /**
     * Variadic arguments always produce integer keys starting from 0, followed by string keys for named arguments.
     */
    private static function hasPositionalKeys(array $queries): bool
    {
        $position = 0;
        foreach ($queries as $key => $query) {
            if (! is_int($key)) {
                continue;
            }

            if ($key !== $position) {
                return false;
            }

            $position++;
        }

        return true;
    }

    /** @psalm-assert-if-true list<FieldQueryInterface> $values */
    private static function isListOfFilters(mixed $values): bool
    {
        if (! is_array($values) || ! array_is_list($values)) {
            return false;
        }

        return array_any($values, static fn ($value): bool => $value instanceof FieldQueryInterface);
    }
}
