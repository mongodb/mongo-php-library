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

/* Create the data encryption keys. Alternatively, the key IDs could be read
 * from a configuration file. */
$keyId1 = $clientEncryption->createDataKey('local');
$keyId2 = $clientEncryption->createDataKey('local');

// Create another client with automatic encryption disabled
$encryptedClient = new Client($uri, [], [
    'autoEncryption' => [
        'keyVaultNamespace' => 'encryption.__keyVault',
        'kmsProviders' => ['local' => ['key' => $localKey]],
        'bypassQueryAnalysis' => true,
    ],
]);

// Define encrypted fields for the collection
$encryptedFields = [
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
];

/* Create the data collection for this script. Specify the "encryptedFields"
 * option to ensure that internal encryption collections are also created. The
 * "encryptedFields" option should also be specified when dropping the
 * collection to ensure that internal encryption collections are dropped. */
$encryptedClient->selectDatabase('test')->createCollection('coll', ['encryptedFields' => $encryptedFields]);
$encryptedCollection = $encryptedClient->selectCollection('test', 'coll');

// Insert a document with manually encrypted fields
$indexedInsertPayload = $clientEncryption->encrypt('indexedValue', [
    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
    'contentionFactor' => 1,
    'keyId' => $keyId1,
]);

$unindexedInsertPayload = $clientEncryption->encrypt('unindexedValue', [
    'algorithm' => ClientEncryption::ALGORITHM_UNINDEXED,
    'keyId' => $keyId2,
]);

$encryptedCollection->insertOne([
    '_id' => 1,
    'encryptedIndexed' => $indexedInsertPayload,
    'encryptedUnindexed' => $unindexedInsertPayload,
]);

/* Encrypt the payload for an "equality" query using the same key that was used
 * to encrypt the corresponding insert payload. */
$indexedFindPayload = $clientEncryption->encrypt('indexedValue', [
    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
    'queryType' => ClientEncryption::QUERY_TYPE_EQUALITY,
    'contentionFactor' => 1,
    'keyId' => $keyId1,
]);

/* Using the client configured with encryption (but not automatic encryption),
 * find the document and observe that the fields are automatically decrypted. */
print_r($encryptedCollection->findOne(['encryptedIndexed' => $indexedFindPayload]));
