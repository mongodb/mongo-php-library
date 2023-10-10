<?php

namespace MongoDB\Builder;

use InvalidArgumentException;
use MongoDB\BSON\Regex;
use MongoDB\Builder\Query\RegexOperator;

use function is_string;

enum Query
{
    use Query\FactoryTrait {
        regex as private generatedRegex;
    }

    /**
     * Selects documents where values match a specified regular expression.
     */
    public static function regex(Regex|string $regex, string $flags = ''): RegexOperator
    {
        if (is_string($regex)) {
            $regex = new Regex($regex, $flags);
        } elseif ($flags !== '') {
            throw new InvalidArgumentException('Flags can only be specified when the regex is a string');
        }

        return self::generatedRegex($regex);
    }
}
