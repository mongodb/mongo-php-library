<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

use function str_repeat;

/**
 * Prose test 4: MongoClient.bulkWrite batch splits when an ops payload exceeds maxMessageSizeBytes
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#4-mongoclientbulkwrite-batch-splits-when-an-ops-payload-exceeds-maxmessagesizebytes
 */
class Prose4_BulkWriteSplitsOnMaxMessageSizeBytesTest extends FunctionalTestCase
{
    public function testSplitOnMaxWriteBatchSize(): void
    {
        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');

        $client = self::createTestClient();

        $hello = $this->getPrimaryServer()->getInfo();
        self::assertIsInt($maxBsonObjectSize = $hello['maxBsonObjectSize'] ?? null);
        self::assertIsInt($maxMessageSizeBytes = $hello['maxMessageSizeBytes'] ?? null);

        $numModels = (int) ($maxMessageSizeBytes / $maxBsonObjectSize + 1);
        $document = ['a' => str_repeat('b', $maxBsonObjectSize - 500)];

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $bulkWrite = ClientBulkWrite::createWithCollection($collection);

        for ($i = 0; $i < $numModels; ++$i) {
            $bulkWrite->insertOne($document);
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

        self::assertSame($numModels, $result->getInsertedCount());
        self::assertCount(2, $subscriber->commandStartedEvents);
        [$firstEvent, $secondEvent] = $subscriber->commandStartedEvents;
        self::assertIsArray($firstCommandOps = $firstEvent->getCommand()->ops ?? null);
        self::assertCount($numModels - 1, $firstCommandOps);
        self::assertIsArray($secondCommandOps = $secondEvent->getCommand()->ops ?? null);
        self::assertCount(1, $secondCommandOps);
        self::assertEquals($firstEvent->getOperationId(), $secondEvent->getOperationId());
    }
}
