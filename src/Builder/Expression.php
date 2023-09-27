<?php

namespace MongoDB\Builder;

use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\Variable;
use stdClass;

class Expression
{
    public function fieldPath(string $path): FieldPath
    {
        return new FieldPath($path);
    }

    public function object(array $args): stdClass
    {
        return (object) $args;
    }

    public function variable(string $name): Variable
    {
        return new Variable($name);
    }
}
