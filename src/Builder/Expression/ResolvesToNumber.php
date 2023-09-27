<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;

interface ResolvesToNumber extends Expression
{
    public const ACCEPTED_TYPES = ['int', 'float', Int64::class, Decimal128::class];
}
