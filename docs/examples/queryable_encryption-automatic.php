<?php

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;

require __DIR__ . '/../../vendor/autoload.php';

$uri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/';

// Generate a secure local key to use for this script
$localKey = new Binary(random_bytes(96));

// Create a client with no encryption options
$client = new Client($uri);

// Create a ClientEncryption object to manage data encryption keys
$clientEncryption = $client->createClientEncryption([
    'keyVaultNamespace' => 'encryption.__keyVault',
    'kmsProviders' => ['local' => ['key' => $localKey]],
]);

/* Create a new key vault collection and data encryption keys for this script.
 * Alternatively, the key IDs could be read from a configuration file. */
$client->selectCollection('encryption', '__keyVault')->drop();
$client->selectCollection('encryption', '__keyVault')->createIndex(['keyAltNames' => 1], ['unique' => true]);
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

/* Create a new collection for this script. The drop and create helpers will
 * infer encryptedFields from the client and manage internal encryption
 * collections automatically. */
$encryptedClient->selectDatabase('test')->dropCollection('coll');
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
