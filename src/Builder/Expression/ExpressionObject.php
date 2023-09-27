<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use stdClass;

final class ExpressionObject implements Expression
{
    public const ACCEPTED_TYPES = ['array', 'stdClass', Document::class, Serializable::class];

    public array|stdClass|Document|Serializable $expression;

    public function __construct(array|stdClass|Document|Serializable $expression)
    {
        $this->expression = $expression;
    }
}
