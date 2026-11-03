<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Exception\BulkWriteCommandException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

/**
 * Prose test 6: MongoClient.bulkWrite handles individual WriteErrors across batches
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#6-mongoclientbulkwrite-handles-individual-writeerrors-across-batches
 */
class Prose6_BulkWriteHandlesWriteErrorsAcrossBatchesTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');
    }

    public function testOrdered(): void
    {
        $client = self::createTestClient();

        $maxWriteBatchSize = $this->getPrimaryServer()->getInfo()['maxWriteBatchSize'] ?? null;
        self::assertIsInt($maxWriteBatchSize);

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection->insertOne(['_id' => 1]);

        $bulkWrite = ClientBulkWrite::createWithCollection($collection, ['ordered' => true]);

        for ($i = 0; $i < $maxWriteBatchSize + 1; ++$i) {
            $bulkWrite->insertOne(['_id' => 1]);
        }

        $subscriber = $this->createSubscriber();
        $client->addSubscriber($subscriber);

        try {
            $client->bulkWrite($bulkWrite);
            self::fail('BulkWriteCommandException was not thrown');
        } catch (BulkWriteCommandException $e) {
            self::assertCount(1, $e->getWriteErrors());
            self::assertSame(1, $subscriber->numBulkWriteObserved);
        }
    }

    public function testUnordered(): void
    {
        $client = self::createTestClient();

        $maxWriteBatchSize = $this->getPrimaryServer()->getInfo()['maxWriteBatchSize'] ?? null;
        self::assertIsInt($maxWriteBatchSize);

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection->insertOne(['_id' => 1]);

        $bulkWrite = ClientBulkWrite::createWithCollection($collection, ['ordered' => false]);

        for ($i = 0; $i < $maxWriteBatchSize + 1; ++$i) {
            $bulkWrite->insertOne(['_id' => 1]);
        }

        $subscriber = $this->createSubscriber();
        $client->addSubscriber($subscriber);

        try {
            $client->bulkWrite($bulkWrite);
            self::fail('BulkWriteCommandException was not thrown');
        } catch (BulkWriteCommandException $e) {
            self::assertCount($maxWriteBatchSize + 1, $e->getWriteErrors());
            self::assertSame(2, $subscriber->numBulkWriteObserved);
        }
    }

    private function createSubscriber(): CommandSubscriber
    {
        return new class implements CommandSubscriber {
            public int $numBulkWriteObserved = 0;

            public function commandStarted(CommandStartedEvent $event): void
            {
                if ($event->getCommandName() === 'bulkWrite') {
                    ++$this->numBulkWriteObserved;
                }
            }

            public function commandSucceeded(CommandSucceededEvent $event): void
            {
            }

            public function commandFailed(CommandFailedEvent $event): void
            {
            }
        };
    }
}
