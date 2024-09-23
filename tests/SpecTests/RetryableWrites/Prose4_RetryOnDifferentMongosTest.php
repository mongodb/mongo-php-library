<?php

namespace MongoDB\Tests\SpecTests\RetryableWrites;

use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

use function assert;
use function count;

/**
 * Prose test 4: Retry on different mongos when one is available
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/retryable-writes/tests/README.md
 */
class Prose4_RetryOnDifferentMongosTest extends FunctionalTestCase
{
    public const HOST_UNREACHABLE = 6;

    public function testRetryOnDifferentMongos(): void
    {
        if (! $this->isMongos()) {
            $this->markTestSkipped('Test requires connections to mongos');
        }

        $this->skipIfServerVersion('<', '4.3.1', 'Test requires configureFailPoint to support errorLabels');

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());

        /* By default, the Manager under test is created with a single-mongos
         * URI. Explicitly create a Client with multiple mongoses and invoke
         * server selection to initialize SDAM. */
        $client = static::createTestClient(static::getUri(true), ['retryWrites' => true]);
        $client->getManager()->selectServer();

        /* Step 1: Select servers for each mongos in the cluster.
         *
         * TODO: Support topologies with 3+ servers by selecting only two and
         * recreating a client URI.
         */
        $servers = $client->getManager()->getServers();
        assert(count($servers) === 2);
        $this->assertNotEquals($servers[0], $servers[1]);

        // Step 2: Configure the following fail point on each mongos
        foreach ($servers as $server) {
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
        }

        /* Step 3: Use the previously created client with retryWrites=true,
         * which is connected to a cluster with two mongoses */

        // Step 4: Enable failed command event monitoring for client
        $subscriber = new class implements CommandSubscriber {
            /** @var string[]  */
            public array $commandFailedServers = [];

            public function commandStarted(CommandStartedEvent $event): void
            {
            }

            public function commandSucceeded(CommandSucceededEvent $event): void
            {
            }

            public function commandFailed(CommandFailedEvent $event): void
            {
                $this->commandFailedServers[] = $event->getHost() . ':' . $event->getPort();
            }
        };

        $client->addSubscriber($subscriber);

        // Step 5: Execute an insert command. Assert that the command failed.
        try {
            $client->selectCollection($this->getDatabaseName(), $this->getCollectionName())->insertOne(['x' => 1]);
            $this->fail('BulkWriteException was not thrown');
        } catch (BulkWriteException $e) {
            $this->assertSame(self::HOST_UNREACHABLE, $e->getCode());
        }

        $client->removeSubscriber($subscriber);

        /* Step 6: Assert that two failed command events occurred. Assert that
         * the failed command events occurred on different mongoses. */
        $this->assertCount(2, $subscriber->commandFailedServers);
        $this->assertNotEquals($subscriber->commandFailedServers[0], $subscriber->commandFailedServers[1]);

        // Step 7: The fail points will be disabled during tearDown()
    }
}
