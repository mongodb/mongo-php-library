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

use IteratorIterator;
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
class ChangeStream extends IteratorIterator
{
}
