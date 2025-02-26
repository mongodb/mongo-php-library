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

use function is_float;
use function is_int;
use function is_string;
use function MongoDB\document_to_array;
use function MongoDB\is_document;
use function sprintf;

/**
 * Index input model class.
 *
 * This class is used to validate user input for index creation.
 *
 * @internal
 * @see \MongoDB\Collection::createIndexes()
 * @see https://github.com/mongodb/specifications/blob/master/source/enumerate-indexes.rst
 * @see https://mongodb.com/docs/manual/reference/method/db.collection.createIndex/
 */
final class IndexInput implements Serializable
{
    /**
     * @param array $index Index specification
     * @throws InvalidArgumentException
     */
    public function __construct(private array $index)
    {
        if (! isset($index['key'])) {
            throw new InvalidArgumentException('Required "key" document is missing from index specification');
        }

        if (! is_document($index['key'])) {
            throw InvalidArgumentException::expectedDocumentType('"key" option', $index['key']);
        }

        foreach ($index['key'] as $fieldName => $order) {
            if (! is_int($order) && ! is_float($order) && ! is_string($order)) {
                throw InvalidArgumentException::invalidType(sprintf('order value for "%s" field within "key" option', $fieldName), $order, 'numeric or string');
            }
        }

        if (! isset($index['name'])) {
            $this->index['name'] = $this->generateIndexName($index['key']);
        }

        if (! is_string($this->index['name'])) {
            throw InvalidArgumentException::invalidType('"name" option', $this->index['name'], 'string');
        }
    }

    /**
     * Return the index name.
     */
    public function __toString(): string
    {
        return $this->index['name'];
    }

    /**
     * Serialize the index information to BSON for index creation.
     *
     * @see \MongoDB\Collection::createIndexes()
     * @see https://php.net/mongodb-bson-serializable.bsonserialize
     */
    public function bsonSerialize(): stdClass
    {
        return (object) $this->index;
    }

    /**
     * Generate an index name from a key specification.
     *
     * @param array|object $document Document containing fields mapped to values,
     *                               which denote order or an index type
     * @throws InvalidArgumentException if $document is not an array or object
     */
    private function generateIndexName(array|object $document): string
    {
        $document = document_to_array($document);

        $name = '';

        foreach ($document as $field => $type) {
            $name .= ($name !== '' ? '_' : '') . $field . '_' . $type;
        }

        return $name;
    }
}
