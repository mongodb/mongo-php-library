<?php

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\ServerException;

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

// Create a data encryption key with an alternate name
$clientEncryption->createDataKey('local', ['keyAltNames' => ['myDataKey']]);

/* Attempt to create a second key with the same name to demonstrate that the
 * unique index is enforced. */
try {
    $clientEncryption->createDataKey('local', ['keyAltNames' => ['myDataKey']]);
} catch (ServerException $e) {
    printf("Error creating key: %s\n", $e->getMessage());
}

// Encrypt a value, using the "keyAltName" option instead of "keyId"
$encryptedValue = $clientEncryption->encrypt('mySecret', [
    'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
    'keyAltName' => 'myDataKey',
]);

print_r($encryptedValue);
