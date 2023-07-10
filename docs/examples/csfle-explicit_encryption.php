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
    'kmsProviders' => [
        'local' => ['key' => $localKey],
    ],
]);

/* Create a new key vault collection and data encryption key for this script.
 * Alternatively, this key ID could be read from a configuration file. */
$client->selectCollection('encryption', '__keyVault')->drop();
$client->selectCollection('encryption', '__keyVault')->createIndex(['keyAltNames' => 1], ['unique' => true]);
$keyId = $clientEncryption->createDataKey('local');

// Create a new collection for this script
$collection = $client->selectCollection('test', 'coll');
$collection->drop();

// Insert a document with a manually encrypted field
$encryptedValue = $clientEncryption->encrypt('mySecret', [
    'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
    'keyId' => $keyId,
]);

$collection->insertOne(['_id' => 1, 'encryptedField' => $encryptedValue]);

/* Using the client configured without encryption, find the document and observe
 * that the field is not automatically decrypted. */
$document = $collection->findOne();

print_r($document);

// Manually decrypt the field
printf("Decrypted: %s\n", $clientEncryption->decrypt($document->encryptedField));
