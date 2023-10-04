<?php

namespace MongoDB\Builder;

use ArrayIterator;
use IteratorAggregate;
use MongoDB\Builder\Stage\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use Traversable;

use function array_is_list;
use function array_merge;

/** @template-implements IteratorAggregate<StageInterface> */
class Pipeline implements IteratorAggregate
{
    /** @var StageInterface[] */
    private array $stages = [];

    public function __construct(StageInterface ...$stages)
    {
        $this->add(...$stages);
    }

    /** @return $this */
    public function add(StageInterface ...$stages): static
    {
        if (! array_is_list($stages)) {
            throw new InvalidArgumentException('Expected $stages argument to be a list, got an associative array.');
        }

        $this->stages = array_merge($this->stages, $stages);

        return $this;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->stages);
    }
}
