<?php
/*
 * Copyright 2016-present MongoDB, Inc.
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
use JsonSerializable;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\LazyBSONArrayCodec;
use MongoDB\Codec\LazyBSONDocumentCodec;
use MongoDB\Exception\InvalidArgumentException;
use ReturnTypeWillChange;

use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_values;
use function is_array;
use function is_object;
use function iterator_to_array;
use function max;
use function sprintf;
use function trigger_error;

use const E_USER_WARNING;

/**
 * Model class for a BSON array.
 *
 * The internal data will be filtered through array_values() during BSON
 * serialization to ensure that it becomes a BSON array.
 *
 * @api
 */
class LazyBSONArray implements ArrayAccess, IteratorAggregate, JsonSerializable, Serializable
{
    /** @var ?CodecLibrary */
    private $library;

    /** @var PackedArray */
    private $bson;

    /** @var array<int, mixed> */
    private $read = [];

    /** @var array<int, true> */
    private $notFound = [];

    /** @var array<int, mixed> */
    private $set = [];

    /** @var array<int, true> */
    private $unset = [];

    /** @var bool */
    private $entirePackedArrayRead = false;

    /**
     * Deep clone this lazy array.
     */
    public function __clone()
    {
        $this->bson = clone $this->bson;

        foreach ($this->set as $key => $value) {
            if (is_object($value)) {
                $this->set[$key] = clone $value;
            }
        }
    }

    /**
     * Constructs a lazy BSON array.
     *
     * @param PackedArray|array|null $input An input for a lazy array.
     *        When given a BSON array, this is treated as input. For lists
     *        this constructs a new BSON array using fromPHP.
     */
    public function __construct($input = null, ?CodecLibrary $library = null)
    {
        if ($input === null) {
            $this->bson = PackedArray::fromPHP([]);
        } elseif ($input instanceof PackedArray) {
            $this->bson = $input;
        } elseif (is_array($input)) {
            $this->bson = PackedArray::fromPHP(array_values($input));
        } else {
            throw InvalidArgumentException::invalidType('input', $input, [PackedArray::class, 'array', 'null']);
        }

        $this->library = $library;
    }

    public function bsonSerialize(): array
    {
        // Always use LazyBSONArrayCodec for BSON serialisation
        $codec = new LazyBSONArrayCodec();
        $codec->attachLibrary($this->getLibrary());

        // @psalm-suppress InvalidReturnStatement
        return $codec->encode($this)->toPHP();
    }

    /** @return Iterator<int, mixed> */
    public function getIterator(): Iterator
    {
        $itemIterator = new AppendIterator();
        // Iterate through all fields in the BSON array
        $itemIterator->append($this->bson->getIterator());
        // Then iterate over all fields that were set
        $itemIterator->append(new ArrayIterator($this->set));

        $seen = [];

        // Use AsArrayIterator to ensure we're indexing from 0 without gaps
        return new AsListIterator(
            new CallbackIterator(
                new CallbackFilterIterator(
                    $itemIterator,
                    /** @param mixed $value */
                    function ($value, int $offset) use (&$seen): bool {
                        // Skip keys that were unset or handled in a previous iterator
                        return ! isset($this->unset[$offset]) && ! isset($seen[$offset]);
                    }
                ),
                /**
                 * @param mixed $value
                 * @return mixed
                 */
                function ($value, int $offset) use (&$seen) {
                    // Mark key as seen, skipping any future occurrences
                    $seen[$offset] = true;

                    // Return actual value (potentially overridden by offsetSet)
                    return $this->offsetGet($offset);
                }
            )
        );
    }

    public function jsonSerialize(): array
    {
        return iterator_to_array($this);
    }

    /** @param mixed $offset */
    public function offsetExists($offset): bool
    {
        $offset = (int) $offset;
        $this->ensureOffsetRead($offset);

        return ! isset($this->unset[$offset]) && ! isset($this->notFound[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $offset = (int) $offset;
        $this->ensureOffsetRead($offset);

        if (isset($this->unset[$offset]) || isset($this->notFound[$offset])) {
            trigger_error(sprintf('Undefined offset: %d', $offset), E_USER_WARNING);
        }

        return array_key_exists($offset, $this->set) ? $this->set[$offset] : $this->read[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->readEntirePackedArray();

            $offset = max(array_merge(
                array_keys($this->read),
                array_keys($this->set),
            )) + 1;
        } else {
            $offset = (int) $offset;
        }

        $this->set[$offset] = $value;
        unset($this->unset[$offset]);
        unset($this->notFound[$offset]);
    }

    /** @param mixed $offset */
    public function offsetUnset($offset): void
    {
        $offset = (int) $offset;
        $this->unset[$offset] = true;
        unset($this->set[$offset]);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function decodeBSONValue($value)
    {
        return $this->getLibrary()->canDecode($value)
            ? $this->getLibrary()->decode($value)
            : $value;
    }

    private function ensureOffsetRead(int $offset): void
    {
        if (isset($this->set[$offset]) || isset($this->notFound[$offset]) || array_key_exists($offset, $this->read)) {
            return;
        }

        if (! $this->bson->has($offset)) {
            $this->notFound[$offset] = true;

            return;
        }

        $value = $this->bson->get($offset);

        // Decode value using the codec library if a codec exists
        $this->read[$offset] = $this->decodeBSONValue($value);
    }

    private function getLibrary(): CodecLibrary
    {
        return $this->library ?? $this->library = new CodecLibrary(new LazyBSONDocumentCodec(), new LazyBSONArrayCodec());
    }

    private function readEntirePackedArray(): void
    {
        if ($this->entirePackedArrayRead) {
            return;
        }

        $this->entirePackedArrayRead = true;

        foreach ($this->bson as $key => $value) {
            if (isset($this->read[$key])) {
                continue;
            }

            $this->read[$key] = $this->decodeBSONValue($value);
        }
    }
}
