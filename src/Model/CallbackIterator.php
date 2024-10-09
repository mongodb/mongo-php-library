<?php
/*
 * Copyright 2017-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Model;

use Iterator;
use IteratorIterator;
use Traversable;

use function call_user_func;

/**
 * Iterator to apply a callback before returning an element
 *
 * @internal
 *
 * @template TKey of array-key
 * @template TValue
 * @template TCallbackValue
 * @template-implements Iterator<TKey, TCallbackValue>
 */
final class CallbackIterator implements Iterator
{
    /** @var callable(TValue, TKey): TCallbackValue */
    private $callback;

    /** @var Iterator<TKey, TValue> */
    private Iterator $iterator;

    /**
     * @param Traversable<TKey, TValue>              $traversable
     * @param callable(TValue, TKey): TCallbackValue $callback
     */
    public function __construct(Traversable $traversable, callable $callback)
    {
        $this->iterator = $traversable instanceof Iterator ? $traversable : new IteratorIterator($traversable);
        $this->callback = $callback;
    }

    /**
     * @see https://php.net/iterator.current
     * @return TCallbackValue
     */
    public function current(): mixed
    {
        return call_user_func($this->callback, $this->iterator->current(), $this->iterator->key());
    }

    /**
     * @see https://php.net/iterator.key
     * @return TKey
     */
    public function key(): mixed
    {
        return $this->iterator->key();
    }

    /** @see https://php.net/iterator.next */
    public function next(): void
    {
        $this->iterator->next();
    }

    /** @see https://php.net/iterator.rewind */
    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    /** @see https://php.net/iterator.valid */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }
}
