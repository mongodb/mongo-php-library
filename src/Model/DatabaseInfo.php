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

/**
 * Database information model class.
 *
 * This class models the database information returned by the listDatabases
 * command. It provides methods to access common database properties.
 *
 * @see \MongoDB\Client::listDatabases()
 * @see https://mongodb.com/docs/manual/reference/command/listDatabases/
 * @template-implements ArrayAccess<string, mixed>
 */
class DatabaseInfo implements ArrayAccess
{
    /** @param array $info Database info */
    public function __construct(private array $info)
    {
    }

    /**
     * Return the database info as an array.
     *
     * @see https://php.net/oop5.magic#language.oop5.magic.debuginfo
     */
    public function __debugInfo(): array
    {
        return $this->info;
    }

    /**
     * Return the database name.
     */
    public function getName(): string
    {
        return (string) $this->info['name'];
    }

    /**
     * Return the databases size on disk (in bytes).
     */
    public function getSizeOnDisk(): int
    {
        /* The MongoDB server might return this number as an integer or float */
        return (integer) $this->info['sizeOnDisk'];
    }

    /**
     * Return whether the database is empty.
     */
    public function isEmpty(): bool
    {
        return (boolean) $this->info['empty'];
    }

    /**
     * Check whether a field exists in the database information.
     *
     * @see https://php.net/arrayaccess.offsetexists
     * @psalm-param array-key $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->info);
    }

    /**
     * Return the field's value from the database information.
     *
     * @see https://php.net/arrayaccess.offsetget
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
