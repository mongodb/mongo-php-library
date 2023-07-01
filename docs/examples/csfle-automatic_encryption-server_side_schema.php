<?php

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;

require __DIR__ . '/../../vendor/autoload.php';

$uri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/';

// Generate a secure local key to use for this script
$localKey = new Binary(random_bytes(96));

/* Create a client with no encryption options. Additionally, create a
 * ClientEncryption object to manage data keys. */
$client = new Client($uri);

$clientEncryption = $client->createClientEncryption([
    'keyVaultNamespace' => 'encryption.__keyVault',
    'kmsProviders' => [
        'local' => ['key' => $localKey],
    ],
]);

/* Drop the key vault collection and create an encryption key. Alternatively,
 * this key ID could be read from a configuration file. */
$client->selectCollection('encryption', '__keyVault')->drop();
$keyId = $clientEncryption->createDataKey('local');

// Create a client with automatic encryption enabled
$encryptedClient = new Client($uri, [], [
    'autoEncryption' => [
        'keyVaultNamespace' => 'encryption.__keyVault',
        'kmsProviders' => ['local' => ['key' => $localKey]],
    ],
]);

/* Drop and create the collection. Specify a validator option when creating the
 * collection to enforce a server-side JSON schema. */
$validator = [
    '$jsonSchema' => [
        'bsonType' => 'object',
        'properties' => [
            'encryptedField' => [
                'encrypt' => [
                    'keyId' => [$keyId],
                    'bsonType' => 'string',
                    'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
                ],
            ],
        ],
    ],
];

$encryptedClient->selectDatabase('test')->dropCollection('coll');
$encryptedClient->selectDatabase('test')->createCollection('coll', ['validator' => $validator]);
$encryptedCollection = $encryptedClient->selectCollection('test', 'coll');

/* Using the encrypted client, insert and find a document. The encrypted field
 * will be automatically encrypted and decrypted. */
$encryptedCollection->insertOne([
    '_id' => 1,
    'encryptedField' => 'mySecret',
]);

print_r($encryptedCollection->findOne(['_id' => 1]));

/* Using the client configured without encryption, find the same document and
 * observe that the field is not automatically decrypted. Additionally, the JSON
 * schema will prohibit inserting a document with an unencrypted field value. */
$unencryptedCollection = $client->selectCollection('test', 'coll');

print_r($unencryptedCollection->findOne(['_id' => 1]));
