<?php

namespace MongoDB\Builder\Expression;

interface ResolvesToMatchExpression
{
    public const ACCEPTED_TYPES = [self::class, 'array', 'object'];
}
