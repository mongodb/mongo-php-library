<?php
/*
 * Copyright 2019 MongoDB, Inc.
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

namespace MongoDB\Model;

use MongoDB\Driver\Cursor;
use IteratorIterator;

/**
 * Iterator for tailable cursors.
 *
 * This iterator may be used to wrap a tailable cursor. By indicating whether
 * the cursor's first batch of results is empty, this iterator can NOP initial
 * calls to rewind() and prevent it from executing a getMore command.
 *
 * @internal
 */
class TailableCursorIterator extends IteratorIterator
{
    private $isRewindNop;

    /**
     * Constructor.
     *
     * @internal
     * @param Cursor  $cursor
     * @param boolean $isFirstBatchIsEmpty
     */
    public function __construct(Cursor $cursor, $isFirstBatchIsEmpty)
    {
        parent::__construct($cursor);
        $this->isRewindNop = $isFirstBatchIsEmpty;
    }

    /**
     * @see https://php.net/iteratoriterator.rewind
     * @return void
     */
    public function next()
    {
        try {
            parent::next();
        } finally {
            /* If the cursor ever advances to a valid position, do not prevent
             * future attempts to rewind the cursor. This will allow the driver
             * to throw a LogicException if the cursor has been advanced past
             * its first element. */
            if ($this->valid()) {
                $this->isRewindNop = false;
            }
        }
    }

    /**
     * @see https://php.net/iteratoriterator.rewind
     * @return void
     */
    public function rewind()
    {
        if ($this->isRewindNop) {
            return;
        }

        parent::rewind();
    }
}
