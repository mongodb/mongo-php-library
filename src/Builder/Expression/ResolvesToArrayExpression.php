<?php

namespace MongoDB\Builder\Expression;

interface ResolvesToArrayExpression
{
    public const ACCEPTED_TYPES = [self::class, 'array', 'object', 'string'];
}
