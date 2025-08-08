<?php

namespace MongoDB\Tests\Database;

use MongoDB\BSON\Binary;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\WriteConcern;

use function iterator_count;
use function str_repeat;

class DropEncryptedCollectionFunctionalTest extends FunctionalTestCase
{
    protected ClientEncryption $clientEncryption;

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfClientSideEncryptionIsNotSupported();

        if ($this->isStandalone()) {
            $this->markTestSkipped('Queryable encryption requires replica sets');
        }

        $this->skipIfServerVersion('<', '7.0.0', 'Queryable encryption requires MongoDB 7.0 or later');

        $client = static::createTestClient();

        // Ensure the key vault collection is dropped before each test
        $collection = $client->selectCollection('keyvault', 'datakeys', ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]);
        $collection->drop();

        $this->clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(str_repeat("\0", 96)) ]],
        ]);
    }

    /** @see https://jira.mongodb.org/browse/PHPLIB-1702 */
    public function testDropCollectionConsultsEncryptedFieldsFromServer(): void
    {
        $originalNumCollections = iterator_count($this->database->listCollectionNames());

        $this->database->createEncryptedCollection(
            $this->getCollectionName(),
            $this->clientEncryption,
            'local',
            null,
            ['encryptedFields' => ['fields' => []]],
        );

        // createEncryptedCollection should create three collections
        $this->assertCount($originalNumCollections + 3, $this->database->listCollectionNames());

        $this->database->dropCollection($this->getCollectionName());

        $this->assertCount($originalNumCollections, $this->database->listCollectionNames());
    }
}
