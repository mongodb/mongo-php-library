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

namespace MongoDB\Operation;

use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;

use function MongoDB\document_to_array;
use function MongoDB\is_document;

/**
 * Drop an encrypted collection.
 *
 * The "encryptedFields" option is required.
 *
 * This operation additionally drops related metadata collections.
 *
 * @internal
 * @see \MongoDB\Database::dropCollection()
 * @see \MongoDB\Collection::drop()
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/client-side-encryption.rst#drop-collection-helper
 * @see https://www.mongodb.com/docs/manual/core/queryable-encryption/fundamentals/manage-collections/
 */
class DropEncryptedCollection implements Executable
{
    private DropCollection $dropCollection;

    /** @var list<DropCollection> */
    private array $dropMetadataCollections;

    /**
     * Constructs an operation to drop an encrypted collection and its related
     * metadata collections.
     *
     * The following option is supported in addition to the options for
     * DropCollection:
     *
     *  * encryptedFields (document): Configuration for encrypted fields.
     *    See: https://www.mongodb.com/docs/manual/core/queryable-encryption/fundamentals/encrypt-and-query/
     *
     * @see DropCollection::__construct() for supported options
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        DropCollection options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, array $options)
    {
        if (! isset($options['encryptedFields'])) {
            throw new InvalidArgumentException('"encryptedFields" option is required');
        }

        if (! is_document($options['encryptedFields'])) {
            throw InvalidArgumentException::expectedDocumentType('"encryptedFields" option', $options['encryptedFields']);
        }

        /** @psalm-var array{ecocCollection?: ?string, escCollection?: ?string} */
        $encryptedFields = document_to_array($options['encryptedFields']);

        $this->dropMetadataCollections = [
            new DropCollection($databaseName, $encryptedFields['escCollection'] ?? 'enxcol_.' . $collectionName . '.esc'),
            new DropCollection($databaseName, $encryptedFields['ecocCollection'] ?? 'enxcol_.' . $collectionName . '.ecoc'),
        ];

        // DropCollection does not use the "encryptedFields" option
        unset($options['encryptedFields']);

        $this->dropCollection = new DropCollection($databaseName, $collectionName, $options);
    }

    /**
     * @see Executable::execute()
     * @return array|object Command result document from dropping the encrypted collection
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        foreach ($this->dropMetadataCollections as $dropMetadataCollection) {
            $dropMetadataCollection->execute($server);
        }

        return $this->dropCollection->execute($server);
    }
}
