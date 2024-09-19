<?php

namespace MongoDB\Tests\SpecTests\RetryableWrites;

use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

/**
 * Prose test 5: Retry on same mongos when not others are available
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/retryable-writes/tests/README.md
 */
class Prose5_RetryOnSameMongosTest extends FunctionalTestCase
{
    public const HOST_UNREACHABLE = 6;

    public function testRetryOnSameMongos(): void
    {
        if (! $this->isMongos()) {
            $this->markTestSkipped('Test requires connections to mongos');
        }

        $this->skipIfServerVersion('<', '4.3.1', 'Test requires configureFailPoint to support errorLabels');

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());

        // Step 1: Create a client that connects to a single mongos
        $client = static::createTestClient(null, ['directConnection' => false, 'retryWrites' => true]);
        $server = $client->getManager()->selectServer();

        // Step 2: Configure the following fail point on the mongos
        $this->configureFailPoint(
            [
                'configureFailPoint' => 'failCommand',
                'mode' => ['times' => 1],
                'data' => [
                    'failCommands' => ['insert'],
                    'errorCode' => self::HOST_UNREACHABLE,
                    'errorLabels' => ['RetryableWriteError'],
                ],
            ],
            $server,
        );

        // Step 3 is omitted because we can re-use the same client

        // Step 4: Enable succeeded and failed command event monitoring
        $subscriber = new class implements CommandSubscriber {
            public ?string $commandSucceededServer = null;
            public ?string $commandFailedServer = null;

            public function commandStarted(CommandStartedEvent $event): void
            {
            }

            public function commandSucceeded(CommandSucceededEvent $event): void
            {
                $this->commandSucceededServer = $event->getHost() . ':' . $event->getPort();
            }

            public function commandFailed(CommandFailedEvent $event): void
            {
                $this->commandFailedServer = $event->getHost() . ':' . $event->getPort();
            }
        };

        $client->addSubscriber($subscriber);

        // Step 5: Execute an insert command. Assert that the command succeeded.
        $insertOneResult = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName())->insertOne(['x' => 1]);
        $this->assertSame(1, $insertOneResult->getInsertedCount());

        $client->removeSubscriber($subscriber);

        /* Step 6: Assert that exactly one failed command event and one
         * succeeded command event occurred. Assert that both events occurred on
         * the same mongos connection. */
        $this->assertNotNull($subscriber->commandSucceededServer);
        $this->assertNotNull($subscriber->commandFailedServer);
        $this->assertEquals($subscriber->commandSucceededServer, $subscriber->commandFailedServer);

        // Step 7: The fail point will be disabled during tearDown()
    }
}
