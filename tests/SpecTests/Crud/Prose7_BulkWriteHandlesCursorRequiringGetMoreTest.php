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
 * Prose test 7: MongoClient.bulkWrite handles a cursor requiring a getMore
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#7-mongoclientbulkwrite-handles-a-cursor-requiring-a-getmore
 */
class Prose7_BulkWriteHandlesCursorRequiringGetMoreTest extends FunctionalTestCase
{
    public function testHandlesCursor(): void
    {
        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');

        $client = self::createTestClient();

        $maxBsonObjectSize = $this->getPrimaryServer()->getInfo()['maxBsonObjectSize'] ?? null;
        self::assertIsInt($maxBsonObjectSize);

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        $bulkWrite = ClientBulkWrite::createWithCollection($collection, ['verboseResults' => true]);
        $bulkWrite->updateOne(
            ['_id' => str_repeat('a', (int) ($maxBsonObjectSize / 2))],
            ['$set' => ['x' => 1]],
            ['upsert' => true],
        );
        $bulkWrite->updateOne(
            ['_id' => str_repeat('b', (int) ($maxBsonObjectSize / 2))],
            ['$set' => ['x' => 1]],
            ['upsert' => true],
        );

        $subscriber = new class implements CommandSubscriber {
            public int $numGetMoreObserved = 0;

            public function commandStarted(CommandStartedEvent $event): void
            {
                if ($event->getCommandName() === 'getMore') {
                    ++$this->numGetMoreObserved;
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

        self::assertSame(2, $result->getUpsertedCount());
        self::assertCount(2, $result->getUpdateResults());
        self::assertSame(1, $subscriber->numGetMoreObserved);
    }
}
