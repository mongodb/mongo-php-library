<?php

namespace MongoDB\Tests\Functions;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Regex;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\FunctionalTestCase;

use function preg_quote;
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

        $encryptionOptions = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => [
                    'key' => new Binary(str_repeat("\0", 96)), // 96-byte local master key
                ],
            ],
        ];
        $client = static::createTestClient(driverOptions: ['autoEncryption' => $encryptionOptions]);

        // Ensure the key vault collection is dropped before each test
        $this->keyVaultCollection = $client->getCollection('keyvault', 'datakeys', ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]);
        $this->keyVaultCollection->drop();

        $this->clientEncryption = $client->createClientEncryption($encryptionOptions);

        $this->database = $client->getDatabase($this->getDatabaseName());
    }

    public function tearDown(): void
    {
        $this->keyVaultCollection?->drop();
    }

    /** @see https://jira.mongodb.org/browse/PHPLIB-1702 */
    public function testDatabaseDropCollectionConsultsEncryptedFieldsFromServer(): void
    {
        $this->database->createEncryptedCollection(
            $this->getCollectionName(),
            $this->clientEncryption,
            'local',
            null,
            ['encryptedFields' => ['fields' => []]],
        );

        $this->assertCountCollections(3, $this->getCollectionName(), 'createEncryptedCollection should create three collections');

        $this->database->dropCollection($this->getCollectionName());

        $this->assertCountCollections(0, $this->getCollectionName());
    }

    /** @see https://jira.mongodb.org/browse/PHPLIB-1702 */
    public function testCollectionDropConsultsEncryptedFieldsFromServer(): void
    {
        $this->database->createEncryptedCollection(
            $this->getCollectionName(),
            $this->clientEncryption,
            'local',
            null,
            ['encryptedFields' => ['fields' => []]],
        );

        $this->assertCountCollections(3, $this->getCollectionName(), 'createEncryptedCollection should create three collections');

        $this->database->getCollection($this->getCollectionName())->drop();

        $this->assertCountCollections(0, $this->getCollectionName());
    }

    private function assertCountCollections(int $expected, $collectionName, string $message = ''): void
    {
        $collectionNames = $this->database->listCollectionNames([
            'filter' => ['name' => new Regex(preg_quote($collectionName))],
        ]);
        $this->assertCount($expected, $collectionNames, $message);
    }
}
