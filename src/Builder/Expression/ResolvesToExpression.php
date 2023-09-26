<?php

namespace MongoDB\Builder\Expression;

interface ResolvesToExpression
{
    public const ACCEPTED_TYPES = [self::class, 'array', 'object', 'string', 'int', 'float', 'bool', 'null'];
}
