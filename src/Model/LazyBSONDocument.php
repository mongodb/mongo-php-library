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

use AppendIterator;
use ArrayAccess;
use ArrayIterator;
use CallbackFilterIterator;
use Iterator;
use IteratorAggregate;
use MongoDB\BSON\Document;
use MongoDB\Exception\InvalidArgumentException;
use ReturnTypeWillChange;

use function array_key_exists;
use function assert;
use function is_array;
use function is_object;
use function is_string;
use function MongoDB\recursive_copy;
use function sprintf;
use function trigger_error;

use const E_USER_WARNING;

/**
 * Model class for a BSON document.
 *
 * The internal data will be cast to an object during BSON serialization to
 * ensure that it becomes a BSON document.
 *
 * @template TValue
 * @template-implements ArrayAccess<string, TValue>
 * @template-implements IteratorAggregate<string, TValue>
 */
class LazyBSONDocument implements ArrayAccess, IteratorAggregate
{
    /** @var Document<TValue> */
    private Document $bson;

    /** @var array<string, TValue> */
    private array $read = [];

    /** @var array<string, bool> */
    private array $exists = [];

    /** @var array<string, TValue> */
    private array $set = [];

    /** @var array<string, true> */
    private array $unset = [];

    /**
     * Deep clone this lazy document.
     */
    public function __clone()
    {
        $this->bson = clone $this->bson;

        foreach ($this->set as $key => $value) {
            $this->set[$key] = recursive_copy($value);
        }
    }

    /**
     * Constructs a lazy BSON document.
     *
     * @param Document<TValue>|array<TValue>|object|null $input An input for a lazy object.
     *        When given a BSON document, this is treated as input. For arrays
     *        and objects this constructs a new BSON document using fromPHP.
     */
    public function __construct($input = null)
    {
        if ($input === null) {
            $this->bson = Document::fromPHP([]);
        } elseif ($input instanceof Document) {
            $this->bson = $input;
        } elseif (is_array($input) || is_object($input)) {
            $this->bson = Document::fromPHP([]);

            foreach ($input as $key => $value) {
                assert(is_string($key));
                $this->set[$key] = $value;
                $this->exists[$key] = true;
            }
        } else {
            throw InvalidArgumentException::invalidType('input', $input, [Document::class, 'array', 'null']);
        }
    }

    /** @return TValue */
    public function __get(string $property)
    {
        $this->readFromBson($property);

        if (isset($this->unset[$property]) || ! $this->exists[$property]) {
            trigger_error(sprintf('Undefined property: %s', $property), E_USER_WARNING);

            return null;
        }

        return array_key_exists($property, $this->set) ? $this->set[$property] : $this->read[$property];
    }

    public function __isset(string $name): bool
    {
        // If we've looked for the value, return the cached result
        if (isset($this->exists[$name])) {
            return $this->exists[$name];
        }

        return $this->exists[$name] = $this->bson->has($name);
    }

    /** @param TValue $value */
    public function __set(string $property, $value): void
    {
        $this->set[$property] = $value;
        unset($this->unset[$property]);
        $this->exists[$property] = true;
    }

    public function __unset(string $name): void
    {
        $this->unset[$name] = true;
        $this->exists[$name] = false;
        unset($this->set[$name]);
    }

    /** @return Iterator<string, TValue> */
    public function getIterator(): CallbackIterator
    {
        $itemIterator = new AppendIterator();
        // Iterate through all fields in the BSON document
        $itemIterator->append($this->bson->getIterator());
        // Then iterate over all fields that were set
        $itemIterator->append(new ArrayIterator($this->set));

        /** @var array<string, bool> $seen */
        $seen = [];

        return new CallbackIterator(
            // Skip keys that were unset or handled in a previous iterator
            new CallbackFilterIterator(
                $itemIterator,
                /** @param TValue $current */
                function ($current, string $key) use (&$seen): bool {
                    return ! isset($this->unset[$key]) && ! isset($seen[$key]);
                },
            ),
            /**
             * @param TValue $value
             * @return TValue
             */
            function ($value, string $key) use (&$seen) {
                // Mark key as seen, skipping any future occurrences
                $seen[$key] = true;

                // Return actual value (potentially overridden by __set)
                return $this->__get($key);
            },
        );
    }

    /** @param mixed $offset */
    public function offsetExists($offset): bool
    {
        return $this->__isset((string) $offset);
    }

    /**
     * @param mixed $offset
     * @return TValue
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->__get((string) $offset);
    }

    /**
     * @param mixed  $offset
     * @param TValue $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set((string) $offset, $value);
    }

    /** @param mixed $offset */
    public function offsetUnset($offset): void
    {
        $this->__unset((string) $offset);
    }

    private function readFromBson(string $key): void
    {
        if (array_key_exists($key, $this->read)) {
            return;
        }

        // Read value if it's present in the BSON structure
        $found = false;
        if ($this->bson->has($key)) {
            $found = true;
            $this->read[$key] = $this->bson->get($key);
        }

        // Mark the offset as "existing" if it wasn't previously marked already
        if (! isset($this->exists[$key])) {
            $this->exists[$key] = $found;
        }
    }
}
