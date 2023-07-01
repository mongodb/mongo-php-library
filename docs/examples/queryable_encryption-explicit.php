<?php

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;

require __DIR__ . '/../../vendor/autoload.php';

$uri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/';

/* Create a local key for this script. In practice, this value would be read
 * from a file, constant, or environment variable. Production apps should use
 * a cloud provider instead of a local key. */
$localKey = new Binary(random_bytes(96));

/* Create a client with no encryption options. Additionally, create a
 * ClientEncryption object to manage data keys. */
$client = new Client($uri);

$clientEncryption = $client->createClientEncryption([
    'keyVaultNamespace' => 'encryption.__keyVault',
    'kmsProviders' => ['local' => ['key' => $localKey]],
]);

// Drop the key vault collection and create two data keys (one for each encrypted field)
$client->selectCollection('encryption', '__keyVault')->drop();
$dataKeyId1 = $clientEncryption->createDataKey('local');
$dataKeyId2 = $clientEncryption->createDataKey('local');

// Create a client with automatic encryption disabled
$encryptedClient = new Client($uri, [], [
    'autoEncryption' => [
        'keyVaultNamespace' => 'encryption.__keyVault',
        'kmsProviders' => ['local' => ['key' => $localKey]],
        'bypassQueryAnalysis' => true,
    ],
]);

// Define encryptedFields for the collection
$encryptedFields = [
    'fields' => [
        [
            'path' => 'encryptedIndexed',
            'bsonType' => 'string',
            'keyId' => $dataKeyId1,
            'queries' => ['queryType' => ClientEncryption::QUERY_TYPE_EQUALITY],
        ],
        [
            'path' => 'encryptedUnindexed',
            'bsonType' => 'string',
            'keyId' => $dataKeyId2,
        ],
    ],
];

/* Drop and create the collection. Pass encryptedFields to each method to ensure
 * that internal encryption collections are managed. */
$encryptedClient->selectDatabase('test')->dropCollection('coll', ['encryptedFields' => $encryptedFields]);
$encryptedClient->selectDatabase('test')->createCollection('coll', ['encryptedFields' => $encryptedFields]);
$collection = $encryptedClient->selectCollection('test', 'coll');

// Insert a document with manually encrypted fields
$indexedValue = 'indexedValue';
$unindexedValue = 'unindexedValue';

$insertPayloadIndexed = $clientEncryption->encrypt($indexedValue, [
    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
    'contentionFactor' => 1,
    'keyId' => $dataKeyId1,
]);

$insertPayloadUnindexed = $clientEncryption->encrypt($unindexedValue, [
    'algorithm' => ClientEncryption::ALGORITHM_UNINDEXED,
    'keyId' => $dataKeyId2,
]);

$collection->insertOne([
    '_id' => 1,
    'encryptedIndexed' => $insertPayloadIndexed,
    'encryptedUnindexed' => $insertPayloadUnindexed,
]);

/* Encrypt the payload for an "equality" query using the same key that was used
 * to encrypt the insert payload. */
$findPayload = $clientEncryption->encrypt($indexedValue, [
    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
    'queryType' => ClientEncryption::QUERY_TYPE_EQUALITY,
    'contentionFactor' => 1,
    'keyId' => $dataKeyId1,
]);

/* Find the inserted document. Fields will still be automatically decrypted
 * because the client was configured with an autoEncryption driver option. */
print_r($collection->findOne(['encryptedIndexed' => $findPayload]));
