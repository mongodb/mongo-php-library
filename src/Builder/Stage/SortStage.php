<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;

class SortStage implements StageInterface
{
    public const NAME = '$sort';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array|object $sort */
    public array|object $sort;

    /**
     * @param array|object $sort
     */
    public function __construct(array|object $sort)
    {
        $this->sort = $sort;
    }
}
