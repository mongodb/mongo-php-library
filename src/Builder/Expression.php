<?php

namespace MongoDB\Builder;

use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\Variable;
use stdClass;

class Expression
{
    public static function fieldPath(string $path): FieldPath
    {
        return new FieldPath($path);
    }

    public static function object(array $args): stdClass
    {
        return (object) $args;
    }

    public static function variable(string $name): Variable
    {
        return new Variable($name);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
