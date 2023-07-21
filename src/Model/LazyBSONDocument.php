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
use Countable;
use Iterator;
use IteratorAggregate;
use JsonSerializable;
use MongoDB\BSON\Document;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\LazyBSONCodecLibrary;
use MongoDB\Exception\InvalidArgumentException;
use ReturnTypeWillChange;

use function array_filter;
use function array_key_exists;
use function array_map;
use function count;
use function get_object_vars;
use function is_array;
use function is_object;
use function is_string;
use function iterator_to_array;
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
final class LazyBSONDocument implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
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

    private bool $entireDocumentRead = false;

    private CodecLibrary $codecLibrary;

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

    /** @param Document<TValue>|array<string, TValue>|object|null $input */
    public function __construct($input = null, ?CodecLibrary $codecLibrary = null)
    {
        if ($input === null) {
            $this->bson = Document::fromPHP([]);
        } elseif ($input instanceof Document) {
            $this->bson = $input;
        } elseif (is_array($input) || is_object($input)) {
            $this->bson = Document::fromPHP([]);

            if (is_object($input)) {
                $input = get_object_vars($input);
            }

            foreach ($input as $key => $value) {
                $this->set[$key] = $value;
                $this->exists[$key] = true;
            }
        } else {
            throw InvalidArgumentException::invalidType('input', $input, [Document::class, 'array', 'null']);
        }

        $this->codecLibrary = $codecLibrary ?? new LazyBSONCodecLibrary();
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
        return $this->exists[$name] ??= $this->bson->has($name);
    }

    /** @return array{bson: Document<TValue>, set: array<string, TValue>, unset: array<string, true>, codecLibrary: CodecLibrary} */
    public function __serialize(): array
    {
        return [
            'bson' => $this->bson,
            'set' => $this->set,
            'unset' => $this->unset,
            'codecLibrary' => $this->codecLibrary,
        ];
    }

    /** @param TValue $value */
    public function __set(string $property, $value): void
    {
        $this->set[$property] = $value;
        unset($this->unset[$property]);
        $this->exists[$property] = true;
    }

    /** @param array{bson: Document<TValue>, set: array<string, TValue>, unset: array<string, true>, codecLibrary: CodecLibrary} $data */
    public function __unserialize(array $data): void
    {
        $this->bson = $data['bson'];
        $this->set = $data['set'];
        $this->unset = $data['unset'];
        $this->codecLibrary = $data['codecLibrary'];

        $this->exists = array_map(
        /** @param TValue $value */
            fn ($value): bool => true,
            $this->set,
        );

        foreach ($this->unset as $name => $unused) {
            $this->exists[$name] = false;
        }
    }

    public function __unset(string $name): void
    {
        $this->unset[$name] = true;
        $this->exists[$name] = false;
        unset($this->set[$name]);
    }

    public function count(): int
    {
        $this->readEntireDocument();

        return count(array_filter($this->exists));
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

    public function jsonSerialize(): array
    {
        return iterator_to_array($this->getIterator());
    }

    /** @param mixed $offset */
    public function offsetExists($offset): bool
    {
        if (! is_string($offset)) {
            return false;
        }

        return $this->__isset($offset);
    }

    /**
     * @param mixed $offset
     * @return TValue
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (! is_string($offset)) {
            trigger_error(sprintf('Undefined offset: %s', (string) $offset), E_USER_WARNING);

            return null;
        }

        return $this->__get($offset);
    }

    /**
     * @param mixed  $offset
     * @param TValue $value
     */
    public function offsetSet($offset, $value): void
    {
        if (! is_string($offset)) {
            trigger_error(sprintf('Unsupported offset: %s', (string) $offset), E_USER_WARNING);

            return;
        }

        $this->__set($offset, $value);
    }

    /** @param mixed $offset */
    public function offsetUnset($offset): void
    {
        if (! is_string($offset)) {
            trigger_error(sprintf('Undefined offset: %s', (string) $offset), E_USER_WARNING);

            return;
        }

        $this->__unset($offset);
    }

    private function readEntireDocument(): void
    {
        if ($this->entireDocumentRead) {
            return;
        }

        foreach ($this->bson as $offset => $value) {
            $this->read[$offset] = $value;

            if (! isset($this->exists[$offset])) {
                $this->exists[$offset] = true;
            }
        }

        $this->entireDocumentRead = true;
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
            $this->read[$key] = $this->codecLibrary->decodeIfSupported($this->bson->get($key));
        }

        // Mark the offset as "existing" if it wasn't previously marked already
        if (! isset($this->exists[$key])) {
            $this->exists[$key] = $found;
        }
    }
}
