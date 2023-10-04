<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;

class AllQuery implements QueryInterface
{
    public const NAME = '$all';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<mixed> ...$value */
    public array $value;

    /**
     * @param mixed $value
     */
    public function __construct(mixed ...$value)
    {
        if (! \array_is_list($value)) {
            throw new \InvalidArgumentException('Expected $value arguments to be a list of mixed, named arguments are not supported');
        }
        if (\count($value) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $value, got %d.', 1, \count($value)));
        }
        $this->value = $value;
    }
}
