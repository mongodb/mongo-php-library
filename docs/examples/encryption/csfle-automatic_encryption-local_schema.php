<?php

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\ServerException;

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
    'kmsProviders' => [
        'local' => ['key' => $localKey],
    ],
]);

/* Create a data encryption key. Alternatively, this key ID could be read from a
 * configuration file. */
$keyId = $clientEncryption->createDataKey('local');

/* Define a JSON schema for the encrypted collection. Since this only utilizes
 * encryption schema syntax, it can be used for both the server-side and local
 * schema. */
$schema = [
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
];

/* Create another client with automatic encryption enabled. Configure a local
 * schema for the encrypted collection using the "schemaMap" option. */
$encryptedClient = new Client($uri, [], [
    'autoEncryption' => [
        'keyVaultNamespace' => 'encryption.__keyVault',
        'kmsProviders' => ['local' => ['key' => $localKey]],
        'schemaMap' => ['test.coll' => $schema],
    ],
]);

/* Create a new collection for this script. Configure a server-side schema by
 * explicitly creating the collection with a "validator" option.
 *
 * Note: without a server-side schema, another client could potentially insert
 * unencrypted data into the collection. Therefore, a local schema should always
 * be used in conjunction with a server-side schema. */
$encryptedClient->selectDatabase('test')->createCollection('coll', ['validator' => ['$jsonSchema' => $schema]]);
$encryptedCollection = $encryptedClient->selectCollection('test', 'coll');

/* Using the encrypted client, insert and find a document to demonstrate that
 * the encrypted field is automatically encrypted and decrypted. */
$encryptedCollection->insertOne(['_id' => 1, 'encryptedField' => 'mySecret']);

print_r($encryptedCollection->findOne(['_id' => 1]));

/* Using the client configured without encryption, find the same document and
 * observe that the field is not automatically decrypted. */
$unencryptedCollection = $client->selectCollection('test', 'coll');

print_r($unencryptedCollection->findOne(['_id' => 1]));

/* Attempt to insert another document with an unencrypted field value to
 * demonstrate that the server-side schema is enforced. */
try {
    $unencryptedCollection->insertOne(['_id' => 2, 'encryptedField' => 'myOtherSecret']);
} catch (ServerException $e) {
    printf("Error inserting document: %s\n", $e->getMessage());
}
