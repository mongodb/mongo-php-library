<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\FieldPathInterface;

class ObjectFieldPath implements FieldPathInterface, ResolvesToObject
{
    public string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }
}
