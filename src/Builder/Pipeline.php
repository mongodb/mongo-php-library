<?php

namespace MongoDB\Builder;

use ArrayIterator;
use IteratorAggregate;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use Traversable;

use function array_is_list;

/** @template-implements IteratorAggregate<StageInterface> */
class Pipeline implements IteratorAggregate
{
    /** @var StageInterface[] */
    private array $stages = [];

    public function __construct(StageInterface|Pipeline ...$stages)
    {
        $this->add(...$stages);
    }

    /** @return $this */
    public function add(StageInterface|Pipeline ...$stages): static
    {
        if (! array_is_list($stages)) {
            throw new InvalidArgumentException('Expected $stages argument to be a list, got an associative array.');
        }

        foreach ($stages as $stage) {
            if ($stage instanceof Pipeline) {
                $this->add(...$stage->stages);
            } else {
                $this->stages[] = $stage;
            }
        }

        return $this;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->stages);
    }
}
