<?php

namespace MongoDB\Builder;

use MongoDB\Builder\Stage\StageInterface;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

class Pipeline
{
    public array $stages;

    public function __construct(
        StageInterface ...$stages
    ) {
        if (! array_is_list($stages)) {
            throw new InvalidArgumentException('Expected $stages argument to be a list, got an associative array.');
        }

        $this->stages = $stages;
    }
}
