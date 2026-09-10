<?php

namespace MongoDB\Tests\SpecTests\ClientBackpressure;

use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

/**
 * Prose test 3: Overload Errors are Retried a Maximum of MAX_RETRIES times
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/client-backpressure/tests/README.md#test-3-overload-errors-are-retried-a-maximum-of-max_retries-times
 */
class Prose3_OverloadErrorMaxRetryTest extends FunctionalTestCase
{
    private const MAX_RETRIES = 2;

    public function testOverloadErrorsAreRetriedMaxRetryTimes(): void
    {
        $this->skipIfServerVersion('<', '4.3.1', 'Test requires configureFailPoint to support errorLabels');

        $client = self::createTestClient();
        $collection = $client->getCollection($this->getDatabaseName(), $this->getCollectionName());

        $subscriber = new class implements CommandSubscriber {
            public int $findCommandsStarted = 0;

            public function commandStarted(CommandStartedEvent $event): void
            {
                if ($event->getCommandName() === 'find') {
                    $this->findCommandsStarted++;
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
            'mode' => 'alwaysOn',
            'data' => [
                'failCommands' => ['find'],
                'errorCode' => 462, // IngressRequestRateLimitExceeded
                'errorLabels' => ['SystemOverloadedError', 'RetryableError'],
            ],
        ]);

        try {
            $collection->find([]);
            $this->fail('Expected RuntimeException was not thrown');
        } catch (RuntimeException $e) {
            $this->assertTrue($e->hasErrorLabel('RetryableError'));
            $this->assertTrue($e->hasErrorLabel('SystemOverloadedError'));
        }

        $client->removeSubscriber($subscriber);

        $this->assertSame(self::MAX_RETRIES + 1, $subscriber->findCommandsStarted);
    }
}
