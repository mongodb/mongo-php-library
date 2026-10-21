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
 * Prose test 5: MongoClient.bulkWrite collects WriteConcernErrors across batches
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#5-mongoclientbulkwrite-collects-writeconcernerrors-across-batches
 */
class Prose5_BulkWriteCollectsWriteConcernErrorsAcrossBatchesTest extends FunctionalTestCase
{
    public function testCollectWriteConcernErrors(): void
    {
        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');

        $client = self::createTestClient(null, ['retryWrites' => false]);

        $maxWriteBatchSize = $this->getPrimaryServer()->getInfo()['maxWriteBatchSize'] ?? null;
        self::assertIsInt($maxWriteBatchSize);

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 2],
            'data' => [
                'failCommands' => ['bulkWrite'],
                'writeConcernError' => [
                    'code' => 91, // ShutdownInProgress
                    'errmsg' => 'Replication is being shut down',
                ],
            ],
        ]);

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $bulkWrite = ClientBulkWrite::createWithCollection($collection);

        for ($i = 0; $i < $maxWriteBatchSize + 1; ++$i) {
            $bulkWrite->insertOne(['a' => 'b']);
        }

        $subscriber = new class implements CommandSubscriber {
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

        $client->addSubscriber($subscriber);

        try {
            $client->bulkWrite($bulkWrite);
            self::fail('BulkWriteCommandException was not thrown');
        } catch (BulkWriteCommandException $e) {
            self::assertCount(2, $e->getWriteConcernErrors());
            $partialResult = $e->getPartialResult();
            self::assertNotNull($partialResult);
            self::assertSame($maxWriteBatchSize + 1, $partialResult->getInsertedCount());
            self::assertSame(2, $subscriber->numBulkWriteObserved);
        }
    }
}
