<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

/**
 * Prose test 3: MongoClient.bulkWrite batch splits a writeModels input with greater than maxWriteBatchSize operations
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#3-mongoclientbulkwrite-batch-splits-a-writemodels-input-with-greater-than-maxwritebatchsize-operations
 */
class Prose3_BulkWriteSplitsOnMaxWriteBatchSizeTest extends FunctionalTestCase
{
    public function testSplitOnMaxWriteBatchSize(): void
    {
        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');

        $client = self::createTestClient();

        $maxWriteBatchSize = $this->getPrimaryServer()->getInfo()['maxWriteBatchSize'] ?? null;
        self::assertIsInt($maxWriteBatchSize);

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $bulkWrite = ClientBulkWrite::createWithCollection($collection);

        for ($i = 0; $i < $maxWriteBatchSize + 1; ++$i) {
            $bulkWrite->insertOne(['a' => 'b']);
        }

        $subscriber = new class implements CommandSubscriber {
            public array $commandStartedEvents = [];

            public function commandStarted(CommandStartedEvent $event): void
            {
                if ($event->getCommandName() === 'bulkWrite') {
                    $this->commandStartedEvents[] = $event;
                }
            }

            public function commandSucceeded(CommandSucceededEvent $event): void
            {
            }

            public function commandFailed(CommandFailedEvent $event): void
            {
            }
        };

        $client->addSubscriber($subscriber);

        $result = $client->bulkWrite($bulkWrite);

        self::assertSame($maxWriteBatchSize + 1, $result->getInsertedCount());
        self::assertCount(2, $subscriber->commandStartedEvents);
        [$firstEvent, $secondEvent] = $subscriber->commandStartedEvents;
        self::assertIsArray($firstCommandOps = $firstEvent->getCommand()->ops ?? null);
        self::assertCount($maxWriteBatchSize, $firstCommandOps);
        self::assertIsArray($secondCommandOps = $secondEvent->getCommand()->ops ?? null);
        self::assertCount(1, $secondCommandOps);
        self::assertEquals($firstEvent->getOperationId(), $secondEvent->getOperationId());
    }
}
