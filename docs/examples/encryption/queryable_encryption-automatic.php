<?php

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;

require __DIR__ . '/../../../vendor/autoload.php';

$uri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/';

/* Note: this script assumes that the test database is empty and that the key
 * vault collection exists and has a partial, unique index on keyAltNames (as
 * demonstrated in the encryption key management scripts). */

// Generate a secure local key to use for this script
$localKey = new Binary(random_bytes(96));

// Create a client with no encryption options
$client = new Client($uri);

// Create a ClientEncryption object to manage data encryption keys
$clientEncryption = $client->createClientEncryption([
    'keyVaultNamespace' => 'encryption.__keyVault',
    'kmsProviders' => ['local' => ['key' => $localKey]],
]);

/* Create the data encryption keys for this script. Alternatively, the key IDs
 * could be read from a configuration file. */
$keyId1 = $clientEncryption->createDataKey('local');
$keyId2 = $clientEncryption->createDataKey('local');

/* Create another client with automatic encryption enabled. Configure the
 * encrypted collection using the "encryptedFields" option. */
$encryptedClient = new Client($uri, [], [
    'autoEncryption' => [
        'keyVaultNamespace' => 'encryption.__keyVault',
        'kmsProviders' => ['local' => ['key' => $localKey]],
        'encryptedFieldsMap' => [
            'test.coll' => [
                'fields' => [
                    [
                        'path' => 'encryptedIndexed',
                        'bsonType' => 'string',
                        'keyId' => $keyId1,
                        'queries' => ['queryType' => ClientEncryption::QUERY_TYPE_EQUALITY],
                    ],
                    [
                        'path' => 'encryptedUnindexed',
                        'bsonType' => 'string',
                        'keyId' => $keyId2,
                    ],
                ],
            ],
        ],
    ],
]);

/* Create the data collection for this script. The create and drop helpers will
 * infer encryptedFields from the client configuration and manage internal
 * encryption collections automatically. Alternatively, the "encryptedFields"
 * option can also be passed explicitly. */
$encryptedClient->selectDatabase('test')->createCollection('coll');
$encryptedCollection = $encryptedClient->selectCollection('test', 'coll');

/* Using the encrypted client, insert a document and find it by querying on the
 * encrypted field. Fields will be automatically encrypted and decrypted. */
$encryptedCollection->insertOne([
    '_id' => 1,
    'encryptedIndexed' => 'indexedValue',
    'encryptedUnindexed' => 'unindexedValue',
]);

print_r($encryptedCollection->findOne(['encryptedIndexed' => 'indexedValue']));

/* Using the client configured without encryption, find the same document and
 * observe that fields are not automatically decrypted. */
$unencryptedCollection = $client->selectCollection('test', 'coll');

print_r($unencryptedCollection->findOne(['_id' => 1]));
