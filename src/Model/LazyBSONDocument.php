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
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\LazyBSONArrayCodec;
use MongoDB\Codec\LazyBSONDocumentCodec;
use MongoDB\Exception\InvalidArgumentException;
use ReturnTypeWillChange;

use function array_key_exists;
use function is_array;
use function is_object;
use function iterator_to_array;
use function sprintf;
use function trigger_error;

use const E_USER_WARNING;

/**
 * Model class for a BSON document.
 *
 * The internal data will be cast to an object during BSON serialization to
 * ensure that it becomes a BSON document.
 *
 * @api
 */
class LazyBSONDocument implements ArrayAccess, IteratorAggregate, JsonSerializable, Serializable
{
    /** @var CodecLibrary|null */
    private $library;

    /** @var Document */
    private $bson;

    /** @var array<string, mixed> */
    private $read = [];

    /** @var array<string, true> */
    private $notFound = [];

    /** @var array<string, mixed> */
    private $set = [];

    /** @var array<string, true> */
    private $unset = [];

    /**
     * Deep clone this lazy document.
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
     * Constructs a lazy BSON document.
     *
     * @param Document|array|object|null $input An input for a lazy object.
     *        When given a BSON document, this is treated as input. For arrays
     *        and objects this constructs a new BSON document using fromPHP.
     */
    public function __construct($input = null, ?CodecLibrary $library = null)
    {
        if ($input === null) {
            $this->bson = Document::fromPHP([]);
        } elseif ($input instanceof Document) {
            $this->bson = $input;
        } elseif (is_array($input) || is_object($input)) {
            $this->bson = Document::fromPHP($input);
        } else {
            throw InvalidArgumentException::invalidType('input', $input, [Document::class, 'array', 'null']);
        }

        $this->library = $library;
    }

    /** @return mixed */
    public function __get(string $property)
    {
        $this->ensureKeyRead($property);

        if (isset($this->unset[$property]) || isset($this->notFound[$property])) {
            trigger_error(sprintf('Undefined property: %s', $property), E_USER_WARNING);
        }

        return array_key_exists($property, $this->set) ? $this->set[$property] : $this->read[$property];
    }

    public function __isset(string $name): bool
    {
        $this->ensureKeyRead($name);

        return ! isset($this->unset[$name]) && ! isset($this->notFound[$name]);
    }

    /** @param mixed $value */
    public function __set(string $property, $value): void
    {
        $this->set[$property] = $value;
        unset($this->unset[$property]);
        unset($this->notFound[$property]);
    }

    public function __unset(string $name): void
    {
        $this->unset[$name] = true;
        unset($this->set[$name]);
    }

    public function bsonSerialize(): object
    {
        // Always use LazyBSONDocumentCodec for BSON serialisation
        $codec = new LazyBSONDocumentCodec();
        $codec->attachLibrary($this->getLibrary());

        // @psalm-suppress InvalidReturnStatement
        return $codec->encode($this)->toPHP();
    }

    /** @return Iterator<string, mixed> */
    public function getIterator(): Iterator
    {
        $itemIterator = new AppendIterator();
        // Iterate through all fields in the BSON document
        $itemIterator->append($this->bson->getIterator());
        // Then iterate over all fields that were set
        $itemIterator->append(new ArrayIterator($this->set));

        $seen = [];

        return new CallbackIterator(
            new CallbackFilterIterator(
                $itemIterator,
                /** @param mixed $current */
                function ($current, string $key) use (&$seen): bool {
                    // Skip keys that were unset or handled in a previous iterator
                    return ! isset($this->unset[$key]) && ! isset($seen[$key]);
                }
            ),
            /**
             * @param mixed $value
             * @return mixed
             */
            function ($value, string $key) use (&$seen) {
                // Mark key as seen, skipping any future occurrences
                $seen[$key] = true;

                // Return actual value (potentially overridden by __set)
                return $this->__get($key);
            }
        );
    }

    public function jsonSerialize(): object
    {
        return (object) iterator_to_array($this);
    }

    /** @param mixed $offset */
    public function offsetExists($offset): bool
    {
        return $this->__isset((string) $offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->__get((string) $offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
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

    private function ensureKeyRead(string $key): void
    {
        if (isset($this->set[$key]) || isset($this->notFound[$key]) || array_key_exists($key, $this->read)) {
            return;
        }

        if (! $this->bson->has($key)) {
            $this->notFound[$key] = true;

            return;
        }

        $value = $this->bson->get($key);

        // Decode value using the codec library if a codec exists
        $this->read[$key] = $this->decodeBSONValue($value);
    }

    private function getLibrary(): CodecLibrary
    {
        return $this->library ?? $this->library = new CodecLibrary(new LazyBSONDocumentCodec(), new LazyBSONArrayCodec());
    }
}
