<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

final class Literal implements Expression
{
    public const ACCEPTED_TYPES = ['mixed'];

    public mixed $expression;

    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
