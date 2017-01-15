<?php

namespace MongoDB;

class CachingIterator implements \Iterator, \Countable
{
    /**
     * @var \Traversable
     */
    private $iterator;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var bool
     */
    private $iteratorExhausted = false;

    /**
     * @param \Traversable $iterator
     */
    public function __construct(\Traversable $iterator)
    {
        $this->iterator = $this->wrapTraversable($iterator);
        $this->storeCurrentItem();
    }

    /**
     * @return int
     */
    public function count()
    {
        $this->exhaustIterator();
        return count($this->items);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * @return void
     */
    public function next()
    {
        if (! $this->iteratorExhausted) {
            $this->iterator->next();
            $this->storeCurrentItem();
        }

        next($this->items);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->exhaustIterator();
        reset($this->items);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * Ensures the original iterator is fully consumed and all items cached
     */
    private function exhaustIterator()
    {
        while (!$this->iteratorExhausted) {
            $this->next();
        }
    }

    /**
     * Stores the current item
     */
    private function storeCurrentItem()
    {
        if (null === $key = $this->iterator->key()) {
            return;
        }

        $this->items[$key] = $this->iterator->current();
    }

    /**
     * @param \Traversable $traversable
     * @return \Generator
     */
    private function wrapTraversable(\Traversable $traversable)
    {
        foreach ($traversable as $key => $value) {
            yield $key => $value;
        }
        $this->iteratorExhausted = true;
    }
}
