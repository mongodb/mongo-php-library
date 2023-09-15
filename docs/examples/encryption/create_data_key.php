<?php

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;

require __DIR__ . '/../../../vendor/autoload.php';

$uri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/';

// Generate a secure local key to use for this script
$localKey = new Binary(random_bytes(96));

// Create a client with no encryption options
$client = new Client($uri);

/* Prepare the database for this script. Drop the key vault collection and
 * ensure it has a unique index for keyAltNames. This would typically be done
 * during application deployment. */
$client->selectCollection('encryption', '__keyVault')->drop();
$client->selectCollection('encryption', '__keyVault')->createIndex(['keyAltNames' => 1], [
    'unique' => true,
    'partialFilterExpression' => ['keyAltNames' => ['$exists' => true]],
]);

// Create a ClientEncryption object to manage data encryption keys
$clientEncryption = $client->createClientEncryption([
    'keyVaultNamespace' => 'encryption.__keyVault',
    'kmsProviders' => [
        'local' => ['key' => $localKey],
    ],
]);

/* Create a data encryption key. To store the key ID for later use, you can use
 * serialize(), var_export(), etc. */
$keyId = $clientEncryption->createDataKey('local');

print_r($keyId);

// Encrypt a value using the key that was just created
$encryptedValue = $clientEncryption->encrypt('mySecret', [
    'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
    'keyId' => $keyId,
]);

print_r($encryptedValue);
