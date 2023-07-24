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
use IteratorAggregate;
use JsonSerializable;
use MongoDB\BSON\PackedArray;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\LazyBSONCodecLibrary;
use MongoDB\Exception\InvalidArgumentException;
use ReturnTypeWillChange;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_values;
use function count;
use function is_array;
use function is_numeric;
use function iterator_to_array;
use function max;
use function MongoDB\recursive_copy;
use function sprintf;
use function trigger_error;

use const E_USER_WARNING;

/**
 * Model class for a BSON array.
 *
 * The internal data will be filtered through array_values() during BSON
 * serialization to ensure that it becomes a BSON array.
 *
 * @template TValue
 * @template-implements ArrayAccess<int, TValue>
 * @template-implements IteratorAggregate<int, TValue>
 */
final class LazyBSONArray implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /** @var PackedArray<TValue> */
    private PackedArray $bson;

    /** @var array<int, TValue> */
    private array $read = [];

    /** @var array<int, bool> */
    private array $exists = [];

    /** @var array<int, TValue> */
    private array $set = [];

    /** @var array<int, true> */
    private array $unset = [];

    private bool $entirePackedArrayRead = false;

    private CodecLibrary $codecLibrary;

    /**
     * Deep clone this lazy array.
     */
    public function __clone()
    {
        $this->bson = clone $this->bson;

        foreach ($this->set as $key => $value) {
            $this->set[$key] = recursive_copy($value);
        }
    }

    /** @param PackedArray<TValue>|list<TValue>|null $input */
    public function __construct($input = null, ?CodecLibrary $codecLibrary = null)
    {
        if ($input === null) {
            $this->bson = PackedArray::fromPHP([]);
        } elseif ($input instanceof PackedArray) {
            $this->bson = $input;
        } elseif (is_array($input)) {
            $this->bson = PackedArray::fromPHP([]);
            $this->set = array_values($input);
            $this->exists = array_map(
                /** @param TValue $value */
                fn ($value): bool => true,
                $this->set,
            );
        } else {
            throw InvalidArgumentException::invalidType('input', $input, [PackedArray::class, 'array', 'null']);
        }

        $this->codecLibrary = $codecLibrary ?? new LazyBSONCodecLibrary();
    }

    /** @return array{bson: PackedArray<TValue>, set: array<int, TValue>, unset: array<int, true>, codecLibrary: CodecLibrary} */
    public function __serialize(): array
    {
        return [
            'bson' => $this->bson,
            'set' => $this->set,
            'unset' => $this->unset,
            'codecLibrary' => $this->codecLibrary,
        ];
    }

    /** @param array{bson: PackedArray<TValue>, set: array<int, TValue>, unset: array<int, true>, codecLibrary: CodecLibrary} $data */
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

        foreach ($this->unset as $index => $unused) {
            $this->exists[$index] = false;
        }
    }

    public function count(): int
    {
        $this->readEntirePackedArray();

        return count(array_filter($this->exists));
    }

    /** @return ListIterator<TValue> */
    public function getIterator(): ListIterator
    {
        $itemIterator = new AppendIterator();
        // Iterate through all fields in the BSON array
        $itemIterator->append($this->bson->getIterator());
        // Then iterate over all fields that were set
        $itemIterator->append(new ArrayIterator($this->set));

        /** @var array<int, bool> $seen */
        $seen = [];

        // Use ListIterator to ensure we're indexing from 0 without gaps
        return new ListIterator(
            new CallbackIterator(
                // Skip keys that were unset or handled in a previous iterator
                new CallbackFilterIterator(
                    $itemIterator,
                    /** @param TValue $value */
                    function ($value, int $offset) use (&$seen): bool {
                        return ! isset($this->unset[$offset]) && ! isset($seen[$offset]);
                    },
                ),
                /**
                 * @param TValue $value
                 * @return TValue
                 */
                function ($value, int $offset) use (&$seen) {
                    // Mark key as seen, skipping any future occurrences
                    $seen[$offset] = true;

                    // Return actual value (potentially overridden by offsetSet)
                    return $this->offsetGet($offset);
                },
            ),
        );
    }

    public function jsonSerialize(): array
    {
        return iterator_to_array($this->getIterator());
    }

    /** @param mixed $offset */
    public function offsetExists($offset): bool
    {
        if (! is_numeric($offset)) {
            return false;
        }

        $offset = (int) $offset;

        return $this->exists[$offset] ??= $this->bson->has($offset);
    }

    /**
     * @param mixed $offset
     * @return TValue
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (! is_numeric($offset)) {
            trigger_error(sprintf('Undefined offset: %s', $offset), E_USER_WARNING);

            return null;
        }

        $offset = (int) $offset;
        $this->readFromBson($offset);

        if (isset($this->unset[$offset]) || ! $this->exists[$offset]) {
            trigger_error(sprintf('Undefined offset: %d', $offset), E_USER_WARNING);

            return null;
        }

        return array_key_exists($offset, $this->set) ? $this->set[$offset] : $this->read[$offset];
    }

    /**
     * @param mixed  $offset
     * @param TValue $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->readEntirePackedArray();

            $existingItems = [
                ...array_keys($this->read),
                ...array_keys($this->set),
            ];

            $offset = $existingItems === [] ? 0 : max($existingItems) + 1;
        } elseif (! is_numeric($offset)) {
            trigger_error(sprintf('Unsupported offset: %s', $offset), E_USER_WARNING);

            return;
        } else {
            $offset = (int) $offset;
        }

        $this->set[$offset] = $value;
        unset($this->unset[$offset]);
        $this->exists[$offset] = true;
    }

    /** @param mixed $offset */
    public function offsetUnset($offset): void
    {
        if (! is_numeric($offset)) {
            trigger_error(sprintf('Undefined offset: %s', $offset), E_USER_WARNING);

            return;
        }

        $offset = (int) $offset;
        $this->unset[$offset] = true;
        $this->exists[$offset] = false;
        unset($this->set[$offset]);
    }

    private function readEntirePackedArray(): void
    {
        if ($this->entirePackedArrayRead) {
            return;
        }

        foreach ($this->bson as $offset => $value) {
            $this->read[$offset] = $value;

            if (! isset($this->exists[$offset])) {
                $this->exists[$offset] = true;
            }
        }

        $this->entirePackedArrayRead = true;
    }

    private function readFromBson(int $offset): void
    {
        if (array_key_exists($offset, $this->read)) {
            return;
        }

        // Read value if it's present in the BSON structure
        $found = false;
        if ($this->bson->has($offset)) {
            $found = true;
            $this->read[$offset] = $this->codecLibrary->decodeIfSupported($this->bson->get($offset));
        }

        // Mark the offset as "existing" if it wasn't previously marked already
        if (! isset($this->exists[$offset])) {
            $this->exists[$offset] = $found;
        }
    }
}
