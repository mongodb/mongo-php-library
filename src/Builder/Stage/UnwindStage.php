<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ArrayFieldPath;

class UnwindStage implements StageInterface
{
    public const NAME = '$unwind';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ArrayFieldPath|non-empty-string $field */
    public ArrayFieldPath|string $field;

    /**
     * @param ArrayFieldPath|non-empty-string $field
     */
    public function __construct(ArrayFieldPath|string $field)
    {
        $this->field = $field;
    }
}
