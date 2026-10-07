<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

use function str_repeat;

/**
 * Prose test 15: MongoClient.bulkWrite with unacknowledged write concern uses w:0 for all batches
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#15-mongoclientbulkwrite-with-unacknowledged-write-concern-uses-w0-for-all-batches
 */
class Prose15_BulkWriteUnacknowledgedWriteConcernTest extends FunctionalTestCase
{
    public function testUnacknowledgedWriteConcern(): void
    {
        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');

        $client = self::createTestClient();

        $hello = $this->getPrimaryServer()->getInfo();
        self::assertIsInt($maxBsonObjectSize = $hello['maxBsonObjectSize'] ?? null);
        self::assertIsInt($maxMessageSizeBytes = $hello['maxMessageSizeBytes'] ?? null);

        // Explicitly create the collection to work around SERVER-95537
        $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $bulkWrite = ClientBulkWrite::createWithCollection($collection, ['ordered' => false]);

        $numModels = (int) ($maxMessageSizeBytes / $maxBsonObjectSize) + 1;

        for ($i = 0; $i < $numModels; ++$i) {
            $bulkWrite->insertOne(['a' => str_repeat('b', $maxBsonObjectSize - 500)]);
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

        $result = $client->bulkWrite($bulkWrite, ['writeConcern' => new WriteConcern(0)]);

        self::assertFalse($result->isAcknowledged());
        self::assertCount(2, $subscriber->commandStartedEvents);
        [$firstEvent, $secondEvent] = $subscriber->commandStartedEvents;

        $firstCommand = $firstEvent->getCommand();
        self::assertIsArray($firstCommand->ops ?? null);
        self::assertCount($numModels - 1, $firstCommand->ops);
        self::assertSame(0, $firstCommand->writeConcern->w ?? null);

        $secondCommand = $secondEvent->getCommand();
        self::assertIsArray($secondCommand->ops ?? null);
        self::assertCount(1, $secondCommand->ops);
        self::assertSame(0, $secondCommand->writeConcern->w ?? null);

        self::assertSame($numModels, $collection->countDocuments());
    }
}
