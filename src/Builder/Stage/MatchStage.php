<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

class MatchStage implements Stage
{
    public const NAME = '$match';
    public const ENCODE = 'single';

    public mixed $query;

    public function __construct(mixed $query)
    {
        $this->query = $query;
    }
}
