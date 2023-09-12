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
    'kmsProviders' => [
        'local' => ['key' => $localKey],
    ],
]);

/* Create a data encryption key. Alternatively, this key ID could be read from a
 * configuration file. */
$keyId = $clientEncryption->createDataKey('local');

// Insert a document with a manually encrypted field
$encryptedValue = $clientEncryption->encrypt('mySecret', [
    'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
    'keyId' => $keyId,
]);

$collection = $client->selectCollection('test', 'coll');
$collection->insertOne(['_id' => 1, 'encryptedField' => $encryptedValue]);

/* Using the client configured without encryption, find the document and observe
 * that the field is not automatically decrypted. */

/** @var object{encryptedField: Binary} $document */
$document = $collection->findOne();

print_r($document);

// Manually decrypt the field
printf("Decrypted: %s\n", $clientEncryption->decrypt($document->encryptedField));
