<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;

/**
 * Retryable writes spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/retryable-writes
 * @group serverless
 */
class RetryableWritesSpecTest extends FunctionalTestCase
{
    public const NOT_PRIMARY = 10107;
    public const SHUTDOWN_IN_PROGRESS = 91;

    /**
     * Prose test 3: when encountering a NoWritesPerformed error after an error with a RetryableWriteError label
     */
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
            private RetryableWritesSpecTest $testCase;

            public function __construct(RetryableWritesSpecTest $testCase)
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
                            'errorCode' => RetryableWritesSpecTest::NOT_PRIMARY,
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

        $client->getManager()->addSubscriber($subscriber);

        // Step 4: Run insertOne
        try {
            $client->selectCollection('db', 'retryable_writes')->insertOne(['write' => 1]);
        } catch (BulkWriteException $e) {
            $writeConcernError = $e->getWriteResult()->getWriteConcernError();
            $this->assertNotNull($writeConcernError);

            // Assert that the write concern error is from the first failpoint
            $this->assertSame(self::SHUTDOWN_IN_PROGRESS, $writeConcernError->getCode());
        }

        // Step 5: Disable the fail point
        $client->getManager()->removeSubscriber($subscriber);
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => 'off',
        ]);
    }
}
