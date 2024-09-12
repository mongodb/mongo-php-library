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

namespace MongoDB\Exception;

class UnsupportedException extends RuntimeException
{
    /**
     * Thrown when the commitQuorum option for createIndexes is not supported
     * by a server.
     *
     * @internal
     */
    public static function commitQuorumNotSupported(): self
    {
        return new self('The "commitQuorum" option is not supported by the server executing this operation');
    }

    /**
     * Thrown when a command's hint option is not supported by a server.
     *
     * @internal
     */
    public static function hintNotSupported(): self
    {
        return new self('Hint is not supported by the server executing this operation');
    }

    /**
     * Thrown when a readConcern is used with a read operation in a transaction.
     *
     * @internal
     */
    public static function readConcernNotSupportedInTransaction(): self
    {
        return new self('The "readConcern" option cannot be specified within a transaction. Instead, specify it when starting the transaction.');
    }

    /**
     * Thrown when a writeConcern is used with a write operation in a transaction.
     *
     * @internal
     */
    public static function writeConcernNotSupportedInTransaction(): self
    {
        return new self('The "writeConcern" option cannot be specified within a transaction. Instead, specify it when starting the transaction.');
    }
}
