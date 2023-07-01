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

/* Drop the key vault collection and create an encryption key with an alternate
 * name. This would typically be done during application deployment. To store
 * the key ID for later use, you can use serialize() or var_export(). */
$client->selectCollection('encryption', '__keyVault')->drop();
$clientEncryption->createDataKey('local', ['keyAltNames' => ['myDataKey']]);

// Encrypt a value, using the "keyAltName" option instead of "keyId"
$encryptedValue = $clientEncryption->encrypt('mySecret', [
    'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
    'keyAltName' => 'myDataKey',
]);

print_r($encryptedValue);
