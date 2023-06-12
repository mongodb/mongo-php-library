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

use MongoDB\BSON\Binary;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;

use function array_key_exists;
use function is_array;
use function is_object;
use function MongoDB\document_to_array;
use function MongoDB\server_supports_feature;

/**
 * Create an encrypted collection.
 *
 * The "encryptedFields" option is required.
 *
 * This operation additionally creates related metadata collections and an index
 * on the encrypted collection.
 *
 * @internal
 * @see \MongoDB\Database::createCollection()
 * @see \MongoDB\Database::createEncryptedCollection()
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/client-side-encryption.rst#create-collection-helper
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/client-side-encryption.rst#create-encrypted-collection-helper
 * @see https://www.mongodb.com/docs/manual/core/queryable-encryption/fundamentals/manage-collections/
 */
class CreateEncryptedCollection implements Executable
{
    private const WIRE_VERSION_FOR_QUERYABLE_ENCRYPTION_V2 = 21;

    /** @var CreateCollection */
    private $createCollection;

    /** @var CreateCollection[] */
    private $createMetadataCollections;

    /** @var CreateIndexes */
    private $createSafeContentIndex;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var array */
    private $options;

    /**
     * @see CreateCollection::__construct() for supported options
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        CreateCollection options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, array $options)
    {
        if (! isset($options['encryptedFields'])) {
            throw new InvalidArgumentException('"encryptedFields" option is required');
        }

        if (! is_array($options['encryptedFields']) && ! is_object($options['encryptedFields'])) {
            throw InvalidArgumentException::invalidType('"encryptedFields" option', $options['encryptedFields'], ['array', 'object']);
        }

        $this->createCollection = new CreateCollection($databaseName, $collectionName, $options);

        /** @psalm-var array{ecocCollection?: ?string, escCollection?: ?string} */
        $encryptedFields = document_to_array($options['encryptedFields']);
        $enxcolOptions = ['clusteredIndex' => ['key' => ['_id' => 1], 'unique' => true]];

        $this->createMetadataCollections = [
            new CreateCollection($databaseName, $encryptedFields['escCollection'] ?? 'enxcol_.' . $collectionName . '.esc', $enxcolOptions),
            new CreateCollection($databaseName, $encryptedFields['ecocCollection'] ?? 'enxcol_.' . $collectionName . '.ecoc', $enxcolOptions),
        ];

        $this->createSafeContentIndex = new CreateIndexes($databaseName, $collectionName, [['key' => ['__safeContent__' => 1]]]);

        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->options = $options;
    }

    /**
     * Create data keys for any encrypted fields where "keyId" is null.
     *
     * This method should be called before execute(), as it may modify the
     * "encryptedFields" option and reconstruct the internal CreateCollection
     * operation used for creating the encrypted collection.
     *
     * The $encryptedFields reference parameter may be used to determine which
     * data keys have been created.
     *
     * @see \MongoDB\Database::createEncryptedCollection()
     * @see https://www.php.net/manual/en/mongodb-driver-clientencryption.createdatakey.php
     * @throws DriverRuntimeException for errors creating a data key
     */
    public function createDataKeys(ClientEncryption $clientEncryption, string $kmsProvider, ?array $masterKey, ?array &$encryptedFields = null): void
    {
        /** @psalm-var array{fields: list<array{keyId: ?Binary}|object{keyId: ?Binary}>|Serializable|PackedArray} */
        $encryptedFields = document_to_array($this->options['encryptedFields']);

        // NOP if there are no fields to examine
        if (! isset($encryptedFields['fields'])) {
            return;
        }

        // Allow PackedArray or Serializable object for the fields array
        if ($encryptedFields['fields'] instanceof PackedArray) {
            /** @psalm-var array */
            $encryptedFields['fields'] = $encryptedFields['fields']->toPHP([
                'array' => 'array',
                'document' => 'object',
                'root' => 'array',
            ]);
        } elseif ($encryptedFields['fields'] instanceof Serializable) {
            $encryptedFields['fields'] = $encryptedFields['fields']->bsonSerialize();
        }

        // Skip invalid types and defer to the server to raise an error
        if (! is_array($encryptedFields['fields'])) {
            return;
        }

        $createDataKeyArgs = [
            $kmsProvider,
            $masterKey !== null ? ['masterKey' => $masterKey] : [],
        ];

        foreach ($encryptedFields['fields'] as $i => $field) {
            // Skip invalid types and defer to the server to raise an error
            if (! is_array($field) && ! is_object($field)) {
                continue;
            }

            $field = document_to_array($field);

            if (array_key_exists('keyId', $field) && $field['keyId'] === null) {
                $field['keyId'] = $clientEncryption->createDataKey(...$createDataKeyArgs);
                $encryptedFields['fields'][$i] = $field;
            }
        }

        $this->options['encryptedFields'] = $encryptedFields;
        $this->createCollection = new CreateCollection($this->databaseName, $this->collectionName, $this->options);
    }

    /**
     * @see Executable::execute()
     * @return array|object Command result document from creating the encrypted collection
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     * @throws UnsupportedException if the server does not support Queryable Encryption
     */
    public function execute(Server $server)
    {
        if (! server_supports_feature($server, self::WIRE_VERSION_FOR_QUERYABLE_ENCRYPTION_V2)) {
            throw new UnsupportedException('Driver support of Queryable Encryption is incompatible with server. Upgrade server to use Queryable Encryption.');
        }

        foreach ($this->createMetadataCollections as $createMetadataCollection) {
            $createMetadataCollection->execute($server);
        }

        $result = $this->createCollection->execute($server);

        $this->createSafeContentIndex->execute($server);

        return $result;
    }
}
