<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

class ObjectIdFieldPath extends FieldPath implements ResolvesToObjectId
{
    public string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }
}
