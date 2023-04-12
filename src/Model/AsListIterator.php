<?php

namespace MongoDB\Model;

use IteratorIterator;
use Traversable;

/**
 * @internal
 * @template TKey as int
 * @template TValue
 * @template TIterator as Traversable<TKey, TValue>
 *
 * @template-extends IteratorIterator<int, TValue, TIterator>
 */
final class AsListIterator extends IteratorIterator
{
    /** @var int */
    private $index = 0;

    public function key(): ?int
    {
        return $this->valid() ? $this->index : null;
    }

    public function next(): void
    {
        $this->index++;

        parent::next();
    }

    public function rewind(): void
    {
        $this->index = 0;

        parent::rewind();
    }
}
