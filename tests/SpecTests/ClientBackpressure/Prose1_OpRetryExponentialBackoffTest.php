<?php

namespace MongoDB\Tests\SpecTests\ClientBackpressure;

use Exception;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Driver\Session;
use MongoDB\Operation\WithTransaction;
use MongoDB\Tests\SpecTests\FunctionalTestCase;
use MongoDB\Tests\UnifiedSpecTests\Util;
use ReflectionException;

/**
 * Prose test 1: Retry operation uses exponential backoff
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/client-backpressure/tests/README.md
 */
class Prose1_OpRetryExponentialBackoffTest extends FunctionalTestCase
{
    /**
     * @throws ReflectionException
     */
    public function testOperationRetryUsesExponentialBackoff()
    {
        $this->skipIfTransactionsAreNotSupported();

        $client = self::createTestClient();
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        $callback = static function (Session $session) use ($collection): void {
            $collection->insertOne(['a' => 1], ['session' => $session]);
        };

        $operation = new WithTransaction($callback);
        $session = $client->startSession();

        Util::setFixedJitter($operation, 0);
        $noBackoffTime = $this->getOperationExecutionTime($session, $operation);

        Util::setFixedJitter($operation, 1);
        $withBackoffTime = $this->getOperationExecutionTime($session, $operation);

        self::assertEqualsWithDelta($noBackoffTime, $withBackoffTime, 2.1);
    }

    /**
     * @throws Exception
     */
    private function getOperationExecutionTime(Session $session, WithTransaction $operation): float
    {
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => 'alwaysOn',
            'data' => [
                'failCommands' => ['insert'],
                'errorCode' => 2,
                'errorLabels' => ['SystemOverloadedError', 'RetryableError'],
            ],
        ]);

        $start = microtime(true);

        try {
            $operation->execute($session);
        } catch (BulkWriteException $e) {
            self::assertInstanceOf(ServerException::class, $e->getPrevious());
        } finally {
            return microtime(true) - $start;
        }
    }
}
