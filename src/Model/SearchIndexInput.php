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

use MongoDB\BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function MongoDB\is_document;

/**
 * Search index input model class.
 *
 * This class is used to validate user input for search index creation.
 *
 * @internal
 * @see \MongoDB\Collection::createSearchIndexes()
 * @see https://github.com/mongodb/specifications/blob/master/source/index-management/index-management.rst#search-indexes
 * @see https://mongodb.com/docs/manual/reference/method/db.collection.createSearchIndex/
 */
final class SearchIndexInput implements Serializable
{
    /**
     * @param array{definition: array|object, name?: string, type?: string} $index Search index specification
     * @throws InvalidArgumentException
     */
    public function __construct(private array $index)
    {
        if (! isset($index['definition'])) {
            throw new InvalidArgumentException('Required "definition" document is missing from search index specification');
        }

        if (! is_document($index['definition'])) {
            throw InvalidArgumentException::expectedDocumentType('"definition" option', $index['definition']);
        }

        // Name is optional, but must be a non-empty string if provided
        if (isset($index['name']) && ! is_string($index['name'])) {
            throw InvalidArgumentException::invalidType('"name" option', $index['name'], 'string');
        }

        if (isset($index['type']) && ! is_string($index['type'])) {
            throw InvalidArgumentException::invalidType('"type" option', $index['type'], 'string');
        }
    }

    /**
     * Serialize the search index information to BSON for search index creation.
     *
     * @see \MongoDB\Collection::createSearchIndexes()
     * @see https://php.net/mongodb-bson-serializable.bsonserialize
     */
    public function bsonSerialize(): stdClass
    {
        return (object) $this->index;
    }
}
