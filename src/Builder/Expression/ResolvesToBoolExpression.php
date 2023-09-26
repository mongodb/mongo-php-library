<?php

namespace MongoDB\Builder\Expression;

interface ResolvesToBoolExpression
{
    public const ACCEPTED_TYPES = [self::class, 'array', 'object', 'string', 'bool'];
}
