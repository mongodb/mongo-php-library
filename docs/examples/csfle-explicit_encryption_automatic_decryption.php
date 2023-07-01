<?php

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;

require __DIR__ . '/../../vendor/autoload.php';

$uri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/';

// Generate a secure local key to use for this script
$localKey = new Binary(random_bytes(96));

// Create a client with automatic encryption disabled
$client = new Client($uri, [], [
    'autoEncryption' => [
        'keyVaultNamespace' => 'encryption.__keyVault',
        'kmsProviders' => ['local' => ['key' => $localKey]],
        'bypassAutoEncryption' => true,
    ],
]);

// Create a ClientEncryption object to manage data keys
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

// Select and drop a collection to use for this example
$collection = $client->selectCollection('test', 'coll');
$collection->drop();

// Insert a document with a manually encrypted field
$encryptedValue = $clientEncryption->encrypt('mySecret', [
    'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
    'keyId' => $keyId,
]);

$collection->insertOne(['encryptedField' => $encryptedValue]);

/* Query for the document. The field will still be automatically decrypted
 * because the client was configured with an autoEncryption driver option. */
$document = $collection->findOne();

print_r($document->encryptedField);
