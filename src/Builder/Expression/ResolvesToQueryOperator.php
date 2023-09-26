<?php

namespace MongoDB\Builder\Expression;

interface ResolvesToQueryOperator
{
    public const ACCEPTED_TYPES = [self::class, 'array', 'object'];
}
