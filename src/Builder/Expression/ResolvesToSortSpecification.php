<?php

namespace MongoDB\Builder\Expression;

interface ResolvesToSortSpecification
{
    public const ACCEPTED_TYPES = [self::class, 'array', 'object'];
}
