<?php

namespace MongoDB\Tests\Type;

use MongoDB\Client;
use MongoDB\Model\AutoEncryptionOptions;
use MongoDB\Model\DriverOptions;

/**
 * Psalm type tests for the URI, driver, and autoEncryption option shapes.
 *
 * This file is not executed by PHPUnit; it is only checked by Psalm to verify
 * that the array shapes defined on Client, DriverOptions, and
 * AutoEncryptionOptions are accepted by their methods and can be imported by
 * downstream consumers.
 *
 * @psalm-import-type AutoEncryptionOptionsArray from AutoEncryptionOptions
 * @psalm-import-type EncryptedFieldsArray from AutoEncryptionOptions
 * @psalm-import-type DriverOptionsArray from DriverOptions
 * @psalm-import-type UriOptionsArray from Client
 */
final class AutoEncryptionShapes
{
    /** @see https://www.mongodb.com/docs/manual/reference/connection-string/ */
    public function constructClientWithUriAndDriverOptions(): void
    {
        new Client(
            null,
            [
                'retryWrites' => true,
                'readPreference' => 'primary',
            ],
            [
                'autoEncryption' => [
                    'keyVaultNamespace' => 'encryption.__keyVault',
                    'kmsProviders' => ['local' => ['key' => 'local-master-key']],
                ],
            ],
        );
    }

    /** Driver options that the library does not model are forwarded to the driver. */
    public function constructClientWithForwardedDriverOption(): void
    {
        new Client(null, [], ['disableClientPersistence' => true]);
    }

    public function createClientEncryptionWithSchemaAndExtraOptions(Client $client): void
    {
        $client->createClientEncryption([
            'keyVaultNamespace' => 'encryption.__keyVault',
            'kmsProviders' => ['aws' => ['accessKeyId' => 'abc', 'secretAccessKey' => 'def']],
            'schemaMap' => ['db.collection' => ['bsonType' => 'object']],
            'extraOptions' => [
                'cryptSharedLibPath' => '/path/to/crypt.so',
                'mongocryptdSpawnArgs' => ['--idleShutdownTimeoutSecs=60'],
            ],
        ]);
    }

    /** Options that fromArray forwards to the driver via miscOptions. */
    public function createClientEncryptionWithForwardedOptions(Client $client): void
    {
        $client->createClientEncryption([
            'keyVaultNamespace' => 'encryption.__keyVault',
            'kmsProviders' => ['local' => ['key' => 'local-master-key']],
            'keyExpirationMS' => 60000,
            'tlsProviders' => [['foo' => 'bar']],
            'disableClientPersistence' => false,
        ]);
    }

    public function createClientEncryptionWithEncryptedFieldsMap(Client $client): void
    {
        $client->createClientEncryption([
            'keyVaultNamespace' => 'encryption.__keyVault',
            'kmsProviders' => ['local' => ['key' => 'local-master-key']],
            'encryptedFieldsMap' => [
                'db.collection' => [
                    'fields' => [
                        [
                            'path' => 'ssn',
                            'bsonType' => 'string',
                            'queries' => [['queryType' => 'equality']],
                        ],
                        [
                            'path' => 'income',
                            'bsonType' => 'int',
                            'queries' => [
                                ['queryType' => 'range', 'min' => 0, 'max' => 1000000, 'sparsity' => 1],
                            ],
                        ],
                    ],
                    'escCollection' => 'enxcol_.collection_esc',
                    'ecocCollection' => 'enxcol_.collection_ecoc',
                ],
            ],
        ]);
    }

    public function buildAutoEncryptionViaFromArray(): void
    {
        AutoEncryptionOptions::fromArray([
            'keyVaultNamespace' => 'encryption.__keyVault',
            'kmsProviders' => ['kmip' => ['endpoint' => 'localhost:5696']],
            'bypassQueryAnalysis' => true,
        ]);
    }

    public function buildDriverOptionsViaFromArray(): void
    {
        DriverOptions::fromArray([
            'typeMap' => ['root' => 'array'],
            'autoEncryption' => [
                'keyVaultNamespace' => 'encryption.__keyVault',
                'kmsProviders' => ['local' => ['key' => 'local-master-key']],
                'encryptedFieldsMap' => ['db.collection' => $this->encryptedFields()],
            ],
        ]);
    }

    /** Exercises the imported UriOptionsArray alias. */
    public function constructClientWithImportedUriOptions(): void
    {
        new Client(null, $this->uriOptions(), []);
    }

    /** @return AutoEncryptionOptionsArray */
    public function autoEncryptionOptions(): array
    {
        return [
            'keyVaultNamespace' => 'encryption.__keyVault',
            'kmsProviders' => ['aws' => ['accessKeyId' => 'abc', 'secretAccessKey' => 'def']],
            'extraOptions' => ['cryptSharedLibPath' => '/path/to/crypt.so'],
        ];
    }

    /** @return EncryptedFieldsArray */
    public function encryptedFields(): array
    {
        return [
            'fields' => [
                ['path' => 'ssn', 'bsonType' => 'string', 'keyId' => null],
            ],
        ];
    }

    /** @return DriverOptionsArray */
    public function driverOptions(): array
    {
        return [
            'typeMap' => ['root' => 'array'],
            'autoEncryption' => $this->autoEncryptionOptions(),
        ];
    }

    /** @return UriOptionsArray */
    public function uriOptions(): array
    {
        return [
            'readPreference' => 'secondary',
            'maxPoolSize' => 10,
        ];
    }
}
