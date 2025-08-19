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
use ReturnTypeWillChange;

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
     * @return array
     */
    public function __debugInfo()
    {
        return $this->info;
    }

    /**
     * Return the database name.
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->info['name'];
    }

    /**
     * Return the databases size on disk (in bytes).
     *
     * @return integer
     */
    public function getSizeOnDisk()
    {
        /* The MongoDB server might return this number as an integer or float */
        return (int) $this->info['sizeOnDisk'];
    }

    /**
     * Return whether the database is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (bool) $this->info['empty'];
    }

    /**
     * Check whether a field exists in the database information.
     *
     * @see https://php.net/arrayaccess.offsetexists
     * @return boolean
     * @psalm-param array-key $offset
     */
    #[ReturnTypeWillChange]
    public function offsetExists(mixed $offset)
    {
        return array_key_exists($offset, $this->info);
    }

    /**
     * Return the field's value from the database information.
     *
     * @see https://php.net/arrayaccess.offsetget
     * @return mixed
     * @psalm-param array-key $offset
     */
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset)
    {
        return $this->info[$offset];
    }

    /**
     * Not supported.
     *
     * @see https://php.net/arrayaccess.offsetset
     * @throws BadMethodCallException
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value)
    {
        throw BadMethodCallException::classIsImmutable(self::class);
    }

    /**
     * Not supported.
     *
     * @see https://php.net/arrayaccess.offsetunset
     * @throws BadMethodCallException
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetUnset(mixed $offset)
    {
        throw BadMethodCallException::classIsImmutable(self::class);
    }
}
