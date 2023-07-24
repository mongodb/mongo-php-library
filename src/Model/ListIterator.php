<?php
/*
 * Copyright 2023-present MongoDB, Inc.
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

use IteratorIterator;
use Traversable;

/**
 * @internal
 *
 * @template TValue
 * @template-extends IteratorIterator<int, TValue, Traversable<mixed, TValue>>
 */
final class ListIterator extends IteratorIterator
{
    private int $index = 0;

    public function key(): int
    {
        return $this->index;
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
