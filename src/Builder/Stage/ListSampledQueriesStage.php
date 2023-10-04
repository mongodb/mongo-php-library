<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;

class ListSampledQueriesStage implements StageInterface
{
    public const NAME = '$listSampledQueries';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Optional|non-empty-string $namespace */
    public Optional|string $namespace;

    /**
     * @param Optional|non-empty-string $namespace
     */
    public function __construct(Optional|string $namespace = Optional::Undefined)
    {
        $this->namespace = $namespace;
    }
}
