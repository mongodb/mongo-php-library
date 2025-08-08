<?php

namespace MongoDB\Tests\Functions;

use MongoDB\BSON\Binary;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\FunctionalTestCase;

use function iterator_count;
use function str_repeat;

class GetEncryptedFieldsFromServerFunctionalTest extends FunctionalTestCase
{
    private ClientEncryption $clientEncryption;
    private Collection $keyVaultCollection;
    private Database $database;

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
        $this->keyVaultCollection = $client->getCollection('keyvault', 'datakeys', ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]);
        $this->keyVaultCollection->drop();

        $this->clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => $this->keyVaultCollection->getNamespace(),
            'kmsProviders' => ['local' => ['key' => new Binary(str_repeat("\0", 96)) ]],
        ]);

        $this->database = $client->getDatabase($this->getDatabaseName());
    }

    public function tearDown(): void
    {
        $this->keyVaultCollection->drop();
    }

    /** @see https://jira.mongodb.org/browse/PHPLIB-1702 */
    public function testDatabaseDropCollectionConsultsEncryptedFieldsFromServer(): void
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

    /** @see https://jira.mongodb.org/browse/PHPLIB-1702 */
    public function testCollectionDropConsultsEncryptedFieldsFromServer(): void
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

        $this->database->getCollection($this->getCollectionName())->drop();

        $this->assertCount($originalNumCollections, $this->database->listCollectionNames());
    }
}
