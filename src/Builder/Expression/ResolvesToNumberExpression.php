<?php

namespace MongoDB\Builder\Expression;

interface ResolvesToNumberExpression
{
    public const ACCEPTED_TYPES = [self::class, 'array', 'object', 'string', 'int', 'float'];
}
