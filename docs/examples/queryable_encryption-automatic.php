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

/* Create a client with automatic encryption enabled. Define encryptedFields for
 * the collection in encryptedFieldsMap. */
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
                        'keyId' => $dataKeyId1,
                        'queries' => ['queryType' => ClientEncryption::QUERY_TYPE_EQUALITY],
                    ],
                    [
                        'path' => 'encryptedUnindexed',
                        'bsonType' => 'string',
                        'keyId' => $dataKeyId2,
                    ],
                ],
            ],
        ],
    ],
]);

/* Drop and create the collection. Each method will infer encryptedFields from
 * the client and manage internal encryption collections automatically. */
$encryptedClient->selectDatabase('test')->dropCollection('coll');
$encryptedClient->selectDatabase('test')->createCollection('coll');
$encryptedCollection = $encryptedClient->selectCollection('test', 'coll');

/* Using the encrypted client, insert a document and find it by querying on the
 * encrypted field. Fields will be automatically encrypted and decrypted. */
$indexedValue = 'indexedValue';
$unindexedValue = 'unindexedValue';

$encryptedCollection->insertOne([
    '_id' => 1,
    'encryptedIndexed' => $indexedValue,
    'encryptedUnindexed' => $unindexedValue,
]);

print_r($encryptedCollection->findOne(['encryptedIndexed' => $indexedValue]));

/* Using the client configured without encryption, find the same document and
 * observe that fields are not automatically decrypted. */
$unencryptedCollection = $client->selectCollection('test', 'coll');

print_r($unencryptedCollection->findOne(['_id' => 1]));
