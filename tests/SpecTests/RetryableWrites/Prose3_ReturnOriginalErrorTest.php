<?php

namespace MongoDB\Tests\SpecTests\RetryableWrites;

use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

/**
 * Prose test 3: Return Original Error
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/retryable-writes/tests/README.md
 * @group serverless
 */
class Prose3_ReturnOriginalErrorTest extends FunctionalTestCase
{
    public const NOT_WRITABLE_PRIMARY = 10107;
    public const SHUTDOWN_IN_PROGRESS = 91;

    public function testNoWritesPerformedErrorReturnsOriginalError(): void
    {
        if (! $this->isReplicaSet()) {
            $this->markTestSkipped('Test only applies to replica sets');
        }

        /* Note: the NoWritesPerformed label was introduced in MongoDB 6.1
         * (SERVER-66479), but the test can still be run on earlier versions. */
        $this->skipIfServerVersion('<', '6.0.0', 'Test should only be run for MongoDB 6.0+');

        $client = self::createTestClient(null, ['retryWrites' => true]);

        // Step 2: Configure a fail point with error code 91
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['insert'],
                'errorLabels' => ['RetryableWriteError'],
                'writeConcernError' => ['code' => self::SHUTDOWN_IN_PROGRESS],
            ],
        ]);

        $subscriber = new class ($this) implements CommandSubscriber {
            private FunctionalTestCase $testCase;

            public function __construct(FunctionalTestCase $testCase)
            {
                $this->testCase = $testCase;
            }

            public function commandStarted(CommandStartedEvent $event): void
            {
            }

            public function commandSucceeded(CommandSucceededEvent $event): void
            {
                if ($event->getCommandName() === 'insert') {
                    // Step 3: Configure a fail point with code 10107
                    $this->testCase->configureFailPoint([
                        'configureFailPoint' => 'failCommand',
                        'mode' => ['times' => 1],
                        'data' => [
                            'errorCode' => Prose3_ReturnOriginalErrorTest::NOT_WRITABLE_PRIMARY,
                            'errorLabels' => ['RetryableWriteError', 'NoWritesPerformed'],
                            'failCommands' => ['insert'],
                        ],
                    ]);
                }
            }

            public function commandFailed(CommandFailedEvent $event): void
            {
            }
        };

        $client->addSubscriber($subscriber);

        // Step 4: Run insertOne
        try {
            $client->selectCollection('db', 'retryable_writes')->insertOne(['write' => 1]);
        } catch (BulkWriteException $e) {
            $writeConcernError = $e->getWriteResult()->getWriteConcernError();
            $this->assertNotNull($writeConcernError);

            // Assert that the write concern error is from the first failpoint
            $this->assertSame(self::SHUTDOWN_IN_PROGRESS, $writeConcernError->getCode());
        }

        $client->removeSubscriber($subscriber);

        // Step 5: The fail point will be disabled during tearDown()
    }
}
