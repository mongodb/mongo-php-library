<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Exception\BulkWriteCommandException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Prose test 22: Top-level error halts batches
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#22-top-level-error-halts-batches
 */
class Prose22_TopLevelErrorHaltsBatchesTest extends FunctionalTestCase
{
    private const ERROR_CODE = 2;

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');
    }

    #[DataProvider('provideOrdered')]
    public function testTopLevelErrorHaltsBatches(bool $ordered): void
    {
        $client = self::createTestClient();

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

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['bulkWrite'],
                'errorCode' => self::ERROR_CODE,
            ],
        ]);

        $maxWriteBatchSize = $this->getPrimaryServer()->getInfo()['maxWriteBatchSize'];
        self::assertIsInt($maxWriteBatchSize);

        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $bulkWrite = ClientBulkWrite::createWithCollection($collection, ['ordered' => $ordered]);

        for ($i = 0; $i < $maxWriteBatchSize + 1; ++$i) {
            $bulkWrite->insertOne(['a' => 'b']);
        }

        try {
            $client->bulkWrite($bulkWrite);
            self::fail('BulkWriteCommandException was not thrown');
        } catch (BulkWriteCommandException $e) {
            $errorReply = $e->getErrorReply();
            self::assertNotNull($errorReply);
            self::assertSame(self::ERROR_CODE, $errorReply['code'] ?? null);

            // No results were observed before the error
            self::assertNull($e->getPartialResult());
            self::assertSame(1, $subscriber->numBulkWriteObserved);
        }
    }

    public static function provideOrdered(): iterable
    {
        yield 'ordered' => [true];
        yield 'unordered' => [false];
    }
}
