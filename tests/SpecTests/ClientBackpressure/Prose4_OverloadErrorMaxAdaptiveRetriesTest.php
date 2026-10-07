<?php

namespace MongoDB\Tests\SpecTests\ClientBackpressure;

use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

/**
 * Prose test 4: Overload Errors are Retried a Maximum of maxAdaptiveRetries times when configured
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/client-backpressure/tests/README.md#test-4-overload-errors-are-retried-a-maximum-of-maxadaptiveretries-times-when-configured
 */
class Prose4_OverloadErrorMaxAdaptiveRetriesTest extends FunctionalTestCase
{
    private const MAX_ADAPTIVE_RETRIES = 1;

    public function testOverloadErrorsAreRetriedMaxAdaptiveRetryTimes(): void
    {
        $this->skipIfServerVersion('<', '4.3.1', 'Test requires configureFailPoint to support errorLabels');

        $client = self::createTestClient(options: ['maxAdaptiveRetries' => self::MAX_ADAPTIVE_RETRIES]);
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

        $this->assertSame(self::MAX_ADAPTIVE_RETRIES + 1, $subscriber->findCommandsStarted);
    }
}
