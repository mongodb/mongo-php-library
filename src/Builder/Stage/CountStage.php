<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;

class CountStage implements StageInterface
{
    public const NAME = '$count';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param non-empty-string $field */
    public string $field;

    /**
     * @param non-empty-string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }
}
