<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Expression\ResolvesToBool;

class GtQuery implements ResolvesToBool
{
    public const NAME = '$gt';
    public const ENCODE = 'single';

    public mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }
}
