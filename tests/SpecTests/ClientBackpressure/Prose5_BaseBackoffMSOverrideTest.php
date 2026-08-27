<?php

namespace MongoDB\Tests\SpecTests\ClientBackpressure;

use MongoDB\Collection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

/**
 * Prose test 5: Overload Errors with baseBackoffMS override base backoff
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/client-backpressure/tests/README.md#test-5-overload-errors-with-basebackoffms-override-base-backoff
 */
class Prose5_BaseBackoffMSOverrideTest extends FunctionalTestCase
{
    private const BASE_BACKOFF_MS = 50;

    public function testServerSuppliedBaseBackoffMSIsAttachedToOverloadErrors(): void
    {
        $this->skipIfServerVersion('<', '9.0', 'Server does not attach baseBackoffMS to overload errors');

        $client = self::createTestClient();
        $collection = $client->getCollection($this->getDatabaseName(), $this->getCollectionName());

        /* The spec asserts that the server attached baseBackoffMS to the error and that the driver parsed it. The
         * backoff itself happens inside ext-mongodb, so the command failed event reply is the only place where this is
         * observable from PHP. libmongoc's own prose test reads it from the same event. */
        $subscriber = new class implements CommandSubscriber {
            public bool $baseBackoffMSWasAttached = false;

            public function commandStarted(CommandStartedEvent $event): void
            {
            }

            public function commandSucceeded(CommandSucceededEvent $event): void
            {
            }

            public function commandFailed(CommandFailedEvent $event): void
            {
                if ($event->getCommandName() !== 'insert') {
                    return;
                }

                $reply = $event->getReply();

                $this->baseBackoffMSWasAttached = isset($reply->baseBackoffMS);
            }
        };

        $client->addSubscriber($subscriber);

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => 'alwaysOn',
            'data' => [
                'failCommands' => ['insert'],
                'errorCode' => 462, // IngressRequestRateLimitExceeded
                'errorLabels' => ['SystemOverloadedError', 'RetryableError'],
            ],
        ]);

        try {
            $this->insertAndExpectOverloadError($collection);
            $this->assertFalse($subscriber->baseBackoffMSWasAttached);

            $this->setExternalClientBaseBackoffMS(self::BASE_BACKOFF_MS);

            $this->insertAndExpectOverloadError($collection);
            $this->assertTrue($subscriber->baseBackoffMSWasAttached);
        } finally {
            // Reset before asserting so a failure cannot leak the parameter into subsequent tests
            $this->setExternalClientBaseBackoffMS(0);
            $client->removeSubscriber($subscriber);
        }

        /* The spec also asserts lower bounds on the duration of each run, which assume that the jitter used for
         * exponential backoff has been pinned to 1. That random number generator lives inside libmongoc and is not
         * reachable from PHPLIB, so with jitter random in [0, 1) those bounds fail regularly. libmongoc covers the
         * timing in test-client-backpressure.c, where it can pin the jitter. */
    }

    private function insertAndExpectOverloadError(Collection $collection): void
    {
        try {
            $collection->insertOne(['a' => 1]);
            $this->fail('Expected RuntimeException was not thrown');
        } catch (RuntimeException $e) {
            $this->assertTrue($e->hasErrorLabel('SystemOverloadedError'));
        }
    }

    private function setExternalClientBaseBackoffMS(int $baseBackoffMS): void
    {
        $this->getPrimaryServer()->executeCommand('admin', new Command([
            'setParameter' => 1,
            'externalClientBaseBackoffMS' => $baseBackoffMS,
        ]));
    }
}
