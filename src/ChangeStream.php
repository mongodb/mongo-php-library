<?php
/*
 * Copyright 2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB;

use MongoDB\Driver\Server;
use MongoDB\Operation\DatabaseCommand;
use Countable;
use Generator;
use Iterator;
use Traversable;

/**
 * Iterator for wrapping a Traversable and caching its results.
 *
 * By caching results, this iterators allows a Traversable to be counted and
 * rewound multiple times, even if the wrapped object does not natively support
 * those operations (e.g. MongoDB\Driver\Cursor).
 *
 * @internal
 */
class ChangeStream implements Iterator
{
    private $items = [];
    private $iterator;
    private $iteratorAdvanced = false;
    private $iteratorExhausted = false;
    private $cursorId;
    private $collectionName;
    private $databaseName;
    private $server;

    /**
     * Constructor.
     *
     * Initialize the iterator and stores the first item in the cache. This
     * effectively rewinds the Traversable and the wrapping Generator, which
     * will execute up to its first yield statement. Additionally, this mimics
     * behavior of the SPL iterators and allows users to omit an explicit call
     * to rewind() before using the other methods.
     *
     * @param Traversable $traversable
     */
    public function __construct(Traversable $traversable, Server $server, $databaseName, $collectionName)
    {
        $this->iterator = $this->wrapTraversable($traversable);
        $this->cursorId = $traversable->getId();
        $this->collectionName = $collectionName;
        $this->databaseName = $databaseName;
        $this->server = $server;
        $this->storeCurrentItem();
    }

    /**
     * @see http://php.net/countable.count
     * @return integer
     */
    public function count()
    {
        $this->exhaustIterator();

        return count($this->items);
    }

    /**
     * @see http://php.net/iterator.current
     * @return mixed
     */
    public function current()
    {
        return $this->iterator->current();
    }

    public function getCursorId()
    {
        return $this->cursorId;
    }

    public function getItems()
    {
        return $this->items;
    }

    /**
     * @see http://php.net/iterator.mixed
     * @return mixed
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * @see http://php.net/iterator.next
     * @return void
     */
    public function next()
    {
        $operation = new DatabaseCommand($this->databaseName, ["getMore" => $this->cursorId, "collection" => $this->collectionName]);
        $operation->execute($this->server);
        $items[] = $operation->toArray();
    }

    /**
     * @see http://php.net/iterator.rewind
     * @return void
     */
    public function rewind()
    {
        /* If the iterator has advanced, exhaust it now so that future iteration
         * can rely on the cache.
         */
        if ($this->iteratorAdvanced) {
            $this->exhaustIterator();
        }

        reset($this->items);
    }

    /**
     * 
     * @see http://php.net/iterator.valid
     * @return boolean
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * Ensures that the inner iterator is fully consumed and cached.
     */
    private function exhaustIterator()
    {
        while ( ! $this->iteratorExhausted) {
            $this->next();
        }
    }

    /**
     * Stores the current item in the cache.
     */
    private function storeCurrentItem()
    {
        $key = $this->iterator->key();

        if ($key === null) {
            return;
        }

        $this->items[$key] = $this->iterator->current();
    }

    /**
     * Wraps the Traversable with a Generator.
     *
     * @param Traversable $traversable
     * @return Generator
     */
    private function wrapTraversable(Traversable $traversable)
    {
        foreach ($traversable as $key => $value) {
            yield $key => $value;
            $this->iteratorAdvanced = true;
        }

        $this->iteratorExhausted = true;
    }
}
