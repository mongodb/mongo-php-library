<?php
/*
 * Copyright 2015-present MongoDB, Inc.
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

use ArrayAccess;
use MongoDB\Exception\BadMethodCallException;

use function array_key_exists;
use function array_search;

/**
 * Index information model class.
 *
 * This class models the index information returned by the listIndexes command
 * or, for legacy servers, queries on the "system.indexes" collection. It
 * provides methods to access common index options, and allows access to other
 * options through the ArrayAccess interface (write methods are not supported).
 * For information on keys and index options, see the referenced
 * db.collection.createIndex() documentation.
 *
 * @see \MongoDB\Collection::listIndexes()
 * @see https://github.com/mongodb/specifications/blob/master/source/enumerate-indexes.rst
 * @see https://mongodb.com/docs/manual/reference/method/db.collection.createIndex/
 * @template-implements ArrayAccess<string, mixed>
 */
class IndexInfo implements ArrayAccess
{
    /** @param array $info Index info */
    public function __construct(private array $info)
    {
    }

    /**
     * Return the collection info as an array.
     *
     * @see https://php.net/oop5.magic#language.oop5.magic.debuginfo
     */
    public function __debugInfo(): array
    {
        return $this->info;
    }

    /**
     * Return the index name to allow casting IndexInfo to string.
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Return the index key.
     */
    public function getKey(): array
    {
        return (array) $this->info['key'];
    }

    /**
     * Return the index name.
     */
    public function getName(): string
    {
        return (string) $this->info['name'];
    }

    /**
     * Return the index version.
     */
    public function getVersion(): int
    {
        return (integer) $this->info['v'];
    }

    /**
     * Return whether or not this index is of type 2dsphere.
     */
    public function is2dSphere(): bool
    {
        return array_search('2dsphere', $this->getKey(), true) !== false;
    }

    /**
     * Return whether this is a sparse index.
     *
     * @see https://mongodb.com/docs/manual/core/index-sparse/
     */
    public function isSparse(): bool
    {
        return ! empty($this->info['sparse']);
    }

    /**
     * Return whether or not this index is of type text.
     */
    public function isText(): bool
    {
        return array_search('text', $this->getKey(), true) !== false;
    }

    /**
     * Return whether this is a TTL index.
     *
     * @see https://mongodb.com/docs/manual/core/index-ttl/
     */
    public function isTtl(): bool
    {
        return array_key_exists('expireAfterSeconds', $this->info);
    }

    /**
     * Return whether this is a unique index.
     *
     * @see https://mongodb.com/docs/manual/core/index-unique/
     */
    public function isUnique(): bool
    {
        return ! empty($this->info['unique']);
    }

    /**
     * Check whether a field exists in the index information.
     *
     * @see https://php.net/arrayaccess.offsetexists
     * @psalm-param array-key $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->info);
    }

    /**
     * Return the field's value from the index information.
     *
     * This method satisfies the Enumerating Indexes specification's requirement
     * that index fields be made accessible under their original names. It may
     * also be used to access fields that do not have a helper method.
     *
     * @see https://php.net/arrayaccess.offsetget
     * @see https://github.com/mongodb/specifications/blob/master/source/enumerate-indexes.rst#getting-full-index-information
     * @psalm-param array-key $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->info[$offset];
    }

    /**
     * Not supported.
     *
     * @see https://php.net/arrayaccess.offsetset
     * @throws BadMethodCallException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw BadMethodCallException::classIsImmutable(self::class);
    }

    /**
     * Not supported.
     *
     * @see https://php.net/arrayaccess.offsetunset
     * @throws BadMethodCallException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw BadMethodCallException::classIsImmutable(self::class);
    }
}
