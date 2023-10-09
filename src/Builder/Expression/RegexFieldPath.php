<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

class RegexFieldPath extends FieldPath implements ResolvesToRegex
{
    public string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }
}
