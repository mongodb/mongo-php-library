<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Exception\BulkWriteCommandException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

use function str_repeat;

/**
 * Prose test 9: MongoClient.bulkWrite handles a getMore error
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#9-mongoclientbulkwrite-handles-a-getmore-error
 */
class Prose9_BulkWriteHandlesGetMoreErrorTest extends FunctionalTestCase
{
    private const UNKNOWN_ERROR = 8;

    public function testHandlesGetMoreError(): void
    {
        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');

        $client = self::createTestClient();

        $maxBsonObjectSize = $this->getPrimaryServer()->getInfo()['maxBsonObjectSize'] ?? null;
        self::assertIsInt($maxBsonObjectSize);

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['getMore'],
                'errorCode' => self::UNKNOWN_ERROR,
            ],
        ]);

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
            public int $numKillCursorsObserved = 0;

            public function commandStarted(CommandStartedEvent $event): void
            {
                if ($event->getCommandName() === 'getMore') {
                    ++$this->numGetMoreObserved;
                } elseif ($event->getCommandName() === 'killCursors') {
                    ++$this->numKillCursorsObserved;
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
            $errorReply = $e->getErrorReply();
            $this->assertNotNull($errorReply);
            $this->assertSame(self::UNKNOWN_ERROR, $errorReply['code'] ?? null);

            // PHPC will also apply the top-level error code to BulkWriteCommandException
            $this->assertSame(self::UNKNOWN_ERROR, $e->getCode());

            $partialResult = $e->getPartialResult();
            self::assertNotNull($partialResult);
            self::assertSame(2, $partialResult->getUpsertedCount());
            self::assertCount(1, $partialResult->getUpdateResults());
            self::assertSame(1, $subscriber->numGetMoreObserved);
            self::assertSame(1, $subscriber->numKillCursorsObserved);
        }
    }
}
