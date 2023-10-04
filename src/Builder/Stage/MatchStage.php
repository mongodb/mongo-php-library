<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Query\QueryInterface;

class MatchStage implements StageInterface
{
    public const NAME = '$match';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array<string, QueryInterface|array|object> ...$query */
    public array $query;

    /**
     * @param QueryInterface|array|object $query
     */
    public function __construct(array|object ...$query)
    {
        foreach($query as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $query arguments to be a map of QueryInterface|array|object, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        if (\count($query) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $query, got %d.', 1, \count($query)));
        }
        $this->query = $query;
    }
}
