<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Query\QueryInterface;
use stdClass;

class MatchStage implements StageInterface
{
    public const NAME = '$match';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param QueryInterface|array|stdClass $query */
    public QueryInterface|stdClass|array $query;

    /**
     * @param QueryInterface|array|stdClass $query
     */
    public function __construct(QueryInterface|stdClass|array $query)
    {
        $this->query = $query;
    }
}
