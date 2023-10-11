<?php

namespace MongoDB\Builder;

use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Query\RegexOperator;
use MongoDB\Builder\Type\QueryFilterInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
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
            $regex = new Regex($regex->getPattern(), $flags);
        }

        return self::generatedRegex($regex);
    }

    public static function query(QueryFilterInterface|QueryInterface|Serializable|array|bool|float|int|null|stdClass|string ...$query): QueryInterface
    {
        return QueryObject::create($query);
    }
}
