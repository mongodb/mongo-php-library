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

namespace MongoDB\Exception;

use Throwable;

use function sprintf;

/**
 * @internal
 * @see \MongoDB\Database::createEncryptedCollection()
 */
final class CreateEncryptedCollectionException extends RuntimeException
{
    public function __construct(Throwable $previous, private array $encryptedFields)
    {
        parent::__construct(sprintf('Creating encrypted collection failed due to previous %s: %s', $previous::class, $previous->getMessage()), 0, $previous);
    }

    /**
     * Returns the encryptedFields option in its last known state before the
     * operation was interrupted.
     *
     * This can be used to infer which data keys were successfully created;
     * however, it is possible that additional data keys were successfully
     * created and are not present in the returned value. For example, if the
     * operation was interrupted by a timeout error when creating a data key,
     * the write may actually have succeeded on the server but the key will not
     * be present in the returned value.
     */
    public function getEncryptedFields(): array
    {
        return $this->encryptedFields;
    }
}
