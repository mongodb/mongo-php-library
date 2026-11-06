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

use function str_repeat;

/**
 * Prose test 23: Top-level error halts getMores
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#23-top-level-error-halts-getmores
 */
class Prose23_TopLevelErrorHaltsGetMoresTest extends FunctionalTestCase
{
    private const ERROR_CODE = 2;

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');
    }

    #[DataProvider('provideOrdered')]
    public function testTopLevelErrorHaltsGetMores(bool $ordered): void
    {
        $client = self::createTestClient();

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

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['getMore'],
                'errorCode' => self::ERROR_CODE,
            ],
        ]);

        $maxBsonObjectSize = $this->getPrimaryServer()->getInfo()['maxBsonObjectSize'];
        self::assertIsInt($maxBsonObjectSize);

        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $bulkWrite = ClientBulkWrite::createWithCollection($collection, [
            'ordered' => $ordered,
            'verboseResults' => true,
        ]);

        foreach (['a', 'b', 'c'] as $id) {
            $bulkWrite->updateOne(
                ['_id' => str_repeat($id, (int) ($maxBsonObjectSize / 2))],
                ['$set' => ['x' => 1]],
                ['upsert' => true],
            );
        }

        try {
            $client->bulkWrite($bulkWrite);
            self::fail('BulkWriteCommandException was not thrown');
        } catch (BulkWriteCommandException $e) {
            $errorReply = $e->getErrorReply();
            self::assertNotNull($errorReply);
            self::assertSame(self::ERROR_CODE, $errorReply['code'] ?? null);

            // Results were observed before the error
            self::assertNotNull($e->getPartialResult());
            self::assertSame(1, $subscriber->numGetMoreObserved);
            self::assertSame(1, $subscriber->numKillCursorsObserved);
        }
    }

    public static function provideOrdered(): iterable
    {
        yield 'ordered' => [true];
        yield 'unordered' => [false];
    }
}
