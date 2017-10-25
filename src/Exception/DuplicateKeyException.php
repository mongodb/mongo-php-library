<?php
/*
 * Copyright 2015-2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Exception;

class DuplicateKeyException extends \MongoDB\Driver\Exception\BulkWriteException implements Exception
{
    /**
     * Thrown when inserting a document with a key that is already present in the database.
     *
     * @param string $key Key of inserted document
     * @return self
     */
    public static function duplicateKeyInsertion($key)
    {
        return new static(sprintf('The key (%s) of this document is already present in the database.', $key));
    }
}
