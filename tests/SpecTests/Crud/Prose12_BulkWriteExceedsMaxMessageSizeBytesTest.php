<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

use function str_repeat;

/**
 * Prose test 12: MongoClient.bulkWrite returns an error if no operations can be added to ops
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#12-mongoclientbulkwrite-returns-an-error-if-no-operations-can-be-added-to-ops
 */
class Prose12_BulkWriteExceedsMaxMessageSizeBytesTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');
    }

    public function testDocumentTooLarge(): void
    {
        $client = self::createTestClient();

        $maxMessageSizeBytes = $this->getPrimaryServer()->getInfo()['maxMessageSizeBytes'] ?? null;
        self::assertIsInt($maxMessageSizeBytes);

        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $bulkWrite = ClientBulkWrite::createWithCollection($collection);
        $bulkWrite->insertOne(['a' => str_repeat('b', $maxMessageSizeBytes)]);

        try {
            $client->bulkWrite($bulkWrite);
            self::fail('Exception was not thrown');
        } catch (InvalidArgumentException $e) {
            self::assertStringContainsString('unable to send document', $e->getMessage());
        }
    }

    public function testNamespaceTooLarge(): void
    {
        $client = self::createTestClient();

        $maxMessageSizeBytes = $this->getPrimaryServer()->getInfo()['maxMessageSizeBytes'] ?? null;
        self::assertIsInt($maxMessageSizeBytes);

        $collectionName = str_repeat('c', $maxMessageSizeBytes);
        $collection = $client->selectCollection($this->getDatabaseName(), $collectionName);
        $bulkWrite = ClientBulkWrite::createWithCollection($collection);
        $bulkWrite->insertOne(['a' => 'b']);

        try {
            $client->bulkWrite($bulkWrite);
            self::fail('Exception was not thrown');
        } catch (InvalidArgumentException $e) {
            self::assertStringContainsString('unable to send document', $e->getMessage());
        }
    }
}
