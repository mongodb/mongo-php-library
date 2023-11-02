<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

use MongoDB\BSON\Type;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_is_list;
use function array_key_exists;
use function array_key_first;
use function array_merge;
use function array_reduce;
use function count;
use function get_debug_type;
use function get_object_vars;
use function is_array;
use function is_string;
use function sprintf;
use function str_starts_with;

/**
 * List of field queries that apply to the same field path.
 */
class CombinedFieldQuery implements FieldQueryInterface
{
    /** @var list<QueryInterface|FieldQueryInterface|Type|stdClass|array|bool|float|int|string|null> $fieldQueries */
    public readonly array $fieldQueries;

    /** @param list<QueryInterface|FieldQueryInterface|Type|stdClass|array|bool|float|int|string|null> $fieldQueries */
    public function __construct(array $fieldQueries)
    {
        if (! array_is_list($fieldQueries)) {
            throw new InvalidArgumentException('Expected filters to be a list, invalid array given.');
        }

        // Flatten nested CombinedFieldQuery
        $this->fieldQueries = array_reduce($fieldQueries, static function (array $fieldQueries, QueryInterface|FieldQueryInterface|Type|stdClass|array|bool|float|int|string|null $fieldQuery): array {
            if ($fieldQuery instanceof CombinedFieldQuery) {
                return array_merge($fieldQueries, $fieldQuery->fieldQueries);
            }

            $fieldQueries[] = $fieldQuery;

            return $fieldQueries;
        }, []);

        // Validate FieldQuery types and non-duplicate operators
        $seenOperators = [];
        foreach ($this->fieldQueries as $fieldQuery) {
            if ($fieldQuery instanceof stdClass) {
                $fieldQuery = get_object_vars($fieldQuery);
            }

            if ($fieldQuery instanceof FieldQueryInterface && $fieldQuery instanceof OperatorInterface) {
                $operator = $fieldQuery->getOperator();
            } elseif (is_array($fieldQuery)) {
                if (count($fieldQuery) !== 1) {
                    throw new InvalidArgumentException(sprintf('Operator must contain exactly one key, %d given', count($fieldQuery)));
                }

                $operator = array_key_first($fieldQuery);
                if (! is_string($operator) || ! str_starts_with($operator, '$')) {
                    throw new InvalidArgumentException(sprintf('Operator must contain exactly one key starting with $, "%s" given', $operator));
                }
            } else {
                throw new InvalidArgumentException(sprintf('Expected filters to be a list of field query operators, array or stdClass, %s given', get_debug_type($fieldQuery)));
            }

            if (array_key_exists($operator, $seenOperators)) {
                throw new InvalidArgumentException(sprintf('Duplicate operator "%s" detected', $operator));
            }

            $seenOperators[$operator] = true;
        }
    }
}
