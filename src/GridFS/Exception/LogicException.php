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

namespace MongoDB\GridFS\Exception;

use LogicException as BaseLogicException;
use MongoDB\Exception\Exception;
use MongoDB\GridFS\CollectionWrapper;

use function get_debug_type;
use function sprintf;

class LogicException extends BaseLogicException implements Exception
{
    /**
     * Throw when a bucket alias is used with global gridfs stream wrapper, but the alias is not registered.
     *
     * @internal
     */
    public static function bucketAliasNotRegistered(string $alias): self
    {
        return new self(sprintf('GridFS stream wrapper has no bucket alias: "%s"', $alias));
    }

    /**
     * Throw when an invalid "gridfs" context option is provided.
     *
     * @param mixed $context
     * @internal
     */
    public static function invalidContext($context): self
    {
        return new self(sprintf('Expected "gridfs" stream context to have type "array" but found "%s"', get_debug_type($context)));
    }

    /**
     * Thrown when a context is provided with an incorrect collection wrapper.
     *
     * @param mixed $object
     * @internal
     */
    public static function invalidContextCollectionWrapper($object): self
    {
        return new self(sprintf('Expected "collectionWrapper" in "gridfs" stream context to have type "%s" but found "%s"', CollectionWrapper::class, get_debug_type($object)));
    }

    /**
     * Thrown when using an unsupported stream mode with fopen('gridfs://...', $mode).
     *
     * @internal
     */
    public static function openModeNotSupported(string $mode): self
    {
        return new self(sprintf('Mode "%s" is not supported by "gridfs://" files. Use one of "r", "rb", "w", or "wb".', $mode));
    }

    /**
     * Thrown when the origin and destination paths are not in the same bucket.
     *
     * @internal
     */
    public static function renamePathMismatch(string $from, string $to): self
    {
        return new self(sprintf('Cannot rename "%s" to "%s" because they are not in the same GridFS bucket.', $from, $to));
    }
}
