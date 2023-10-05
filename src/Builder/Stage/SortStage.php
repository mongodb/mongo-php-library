<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use stdClass;

class SortStage implements StageInterface
{
    public const NAME = '$sort';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array|stdClass $sort */
    public stdClass|array $sort;

    /**
     * @param array|stdClass $sort
     */
    public function __construct(stdClass|array $sort)
    {
        $this->sort = $sort;
    }
}
