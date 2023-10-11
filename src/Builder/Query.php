<?php

namespace MongoDB\Builder;

use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Query\RegexOperator;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;

enum Query
{
    use Query\FactoryTrait {
        regex as private generatedRegex;
    }

    /**
     * Selects documents where values match a specified regular expression.
     */
    public static function regex(Regex|string $regex, ?string $flags = null): RegexOperator
    {
        if (is_string($regex)) {
            $regex = new Regex($regex, $flags ?? '');
        } elseif (is_string($flags)) {
            throw new InvalidArgumentException('Regex flags must be specified as part of the Regex object');
        }

        return self::generatedRegex($regex);
    }

    public static function query(FieldQueryInterface|QueryInterface|Serializable|array|bool|float|int|null|stdClass|string ...$query): QueryInterface
    {
        return QueryObject::create($query);
    }
}
