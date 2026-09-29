<?php

namespace MongoDB\Tests\Type;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Model\AutoEncryptionOptions;
use MongoDB\Model\DriverOptions;
use stdClass;

/**
 * Psalm type tests for the URI, driver, and autoEncryption option shapes.
 *
 * This file is not executed by PHPUnit; it is only checked by Psalm to verify
 * that the array shapes defined on Client, DriverOptions, and
 * AutoEncryptionOptions are accepted by their methods and can be imported by
 * downstream consumers.
 *
 * @psalm-import-type AutoEncryptionOptionsShape from AutoEncryptionOptions
 * @psalm-import-type EncryptedFieldsShape from AutoEncryptionOptions
 * @psalm-import-type KmsProvidersShape from AutoEncryptionOptions
 * @psalm-import-type DriverOptionsShape from DriverOptions
 * @psalm-import-type UriOptionsShape from Client
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
            'tlsOptions' => ['kmip' => ['tlsCAFile' => '/path/to/ca.pem']],
            'schemaMap' => ['db.collection' => ['bsonType' => 'object']],
            'bypassAutoEncryption' => true,
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

    /** Exercises the imported UriOptionsShape alias. */
    public function constructClientWithImportedUriOptions(): void
    {
        new Client(null, $this->uriOptions(), []);
    }

    /** @return AutoEncryptionOptionsShape */
    public function autoEncryptionOptions(): array
    {
        return [
            'keyVaultNamespace' => 'encryption.__keyVault',
            'kmsProviders' => ['aws' => ['accessKeyId' => 'abc', 'secretAccessKey' => 'def']],
            'extraOptions' => ['cryptSharedLibPath' => '/path/to/crypt.so'],
        ];
    }

    /**
     * Keys may be a provider name or a provider name suffixed with ":<name>".
     *
     * @return KmsProvidersShape
     */
    public function kmsProviders(): array
    {
        return [
            'local' => ['key' => 'local-master-key'],
            'aws' => ['accessKeyId' => 'abc', 'secretAccessKey' => 'def'],
            'aws:name2' => ['accessKeyId' => 'foo2', 'secretAccessKey' => 'bar2'],
            'local:name1' => ['key' => 'another-master-key'],
        ];
    }

    /** Object-form KMS providers are valid for on-demand credentials. */
    public function createClientEncryptionWithObjectKmsProviders(Client $client): void
    {
        $kmsProviders = new stdClass();
        $kmsProviders->aws = new stdClass();

        $client->createClientEncryption(['kmsProviders' => $kmsProviders]);
    }

    /** @return EncryptedFieldsShape */
    public function encryptedFields(): array
    {
        return [
            'fields' => [
                ['path' => 'ssn', 'bsonType' => 'string', 'keyId' => null],
                ['path' => 'indexed', 'bsonType' => 'string', 'keyAltName' => 'altname'],
                [
                    'path' => 'balance',
                    'bsonType' => 'decimal',
                    'queries' => [
                        [
                            'queryType' => 'range',
                            'min' => new Decimal128('0'),
                            'max' => new Decimal128('1000000'),
                            'trimFactor' => 1,
                            'precision' => 2,
                            'contention' => 0,
                        ],
                    ],
                ],
                [
                    'path' => 'birthday',
                    'bsonType' => 'date',
                    'queries' => [
                        [
                            'queryType' => 'range',
                            'min' => new UTCDateTime(0),
                            'max' => new UTCDateTime(200),
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @return DriverOptionsShape */
    public function driverOptions(): array
    {
        return [
            'typeMap' => ['root' => 'array'],
            'autoEncryption' => $this->autoEncryptionOptions(),
        ];
    }

    /** @return UriOptionsShape */
    public function uriOptions(): array
    {
        return [
            'readPreference' => 'secondary',
            'maxPoolSize' => 10,
        ];
    }
}
