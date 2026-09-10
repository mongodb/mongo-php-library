<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\Client;
use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

use function str_repeat;

/**
 * Prose test 11: MongoClient.bulkWrite batch splits when the addition of a new namespace exceeds the maximum message size
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#11-mongoclientbulkwrite-batch-splits-when-the-addition-of-a-new-namespace-exceeds-the-maximum-message-size
 */
class Prose11_BulkWriteBatchSplitsWhenNamespaceExceedsMessageSizeTest extends FunctionalTestCase
{
    private Client $client;
    private ClientBulkWrite $bulkWrite;
    private int $numModels;

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');

        $this->client = self::createTestClient();

        $hello = $this->getPrimaryServer()->getInfo();
        self::assertIsInt($maxBsonObjectSize = $hello['maxBsonObjectSize'] ?? null);
        self::assertIsInt($maxMessageSizeBytes = $hello['maxMessageSizeBytes'] ?? null);

        $opsBytes = $maxMessageSizeBytes - 1122;
        $this->numModels = (int) ($opsBytes / $maxBsonObjectSize);
        $remainderBytes = $opsBytes % $maxBsonObjectSize;

        // Use namespaces specific to the test, as they are relevant to batch calculations
        $this->dropCollection('db', 'coll');
        $collection = $this->client->selectCollection('db', 'coll');

        $this->bulkWrite = ClientBulkWrite::createWithCollection($collection);

        for ($i = 0; $i < $this->numModels; ++$i) {
            $this->bulkWrite->insertOne(['a' => str_repeat('b', $maxBsonObjectSize - 57)]);
        }

        if ($remainderBytes >= 217) {
            ++$this->numModels;
            $this->bulkWrite->insertOne(['a' => str_repeat('b', $remainderBytes - 57)]);
        }
    }

    public function testNoBatchSplittingRequired(): void
    {
        $subscriber = $this->createSubscriber();
        $this->client->addSubscriber($subscriber);

        $this->bulkWrite->insertOne(['a' => 'b']);

        $result = $this->client->bulkWrite($this->bulkWrite);

        self::assertSame($this->numModels + 1, $result->getInsertedCount());
        self::assertCount(1, $subscriber->commandStartedEvents);
        $command = $subscriber->commandStartedEvents[0]->getCommand();
        self::assertCount($this->numModels + 1, $command->ops);
        self::assertCount(1, $command->nsInfo);
        self::assertSame('db.coll', $command->nsInfo[0]->ns ?? null);
    }

    public function testBatchSplittingRequired(): void
    {
        $subscriber = $this->createSubscriber();
        $this->client->addSubscriber($subscriber);

        $secondCollectionName = str_repeat('c', 200);
        $this->dropCollection('db', $secondCollectionName);
        $secondCollection = $this->client->selectCollection('db', $secondCollectionName);
        $this->bulkWrite->withCollection($secondCollection)->insertOne(['a' => 'b']);

        $result = $this->client->bulkWrite($this->bulkWrite);

        self::assertSame($this->numModels + 1, $result->getInsertedCount());
        self::assertCount(2, $subscriber->commandStartedEvents);
        [$firstEvent, $secondEvent] = $subscriber->commandStartedEvents;

        $firstCommand = $firstEvent->getCommand();
        self::assertCount($this->numModels, $firstCommand->ops);
        self::assertCount(1, $firstCommand->nsInfo);
        self::assertSame('db.coll', $firstCommand->nsInfo[0]->ns ?? null);

        $secondCommand = $secondEvent->getCommand();
        self::assertCount(1, $secondCommand->ops);
        self::assertCount(1, $secondCommand->nsInfo);
        self::assertSame($secondCollection->getNamespace(), $secondCommand->nsInfo[0]->ns ?? null);
    }

    private function createSubscriber(): CommandSubscriber
    {
        return new class implements CommandSubscriber {
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
    }
}
