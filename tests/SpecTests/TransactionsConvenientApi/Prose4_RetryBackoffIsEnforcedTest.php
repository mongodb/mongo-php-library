<?php

namespace MongoDB\Tests\SpecTests\TransactionsConvenientApi;

use MongoDB\Driver\Session;
use MongoDB\Operation\WithTransaction;
use MongoDB\Tests\SpecTests\FunctionalTestCase;
use ReflectionProperty;

use function microtime;

/** @see https://github.com/mongodb/specifications/tree/master/source/transactions-convenient-api/tests#retry-backoff-is-enforced */
class Prose4_RetryBackoffIsEnforcedTest extends FunctionalTestCase
{
    public function testBackoffIsEnforced(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        $client = self::createTestClient(static::getUri());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        // Create collection before transaction, as MongoDB 4.2 doesn't allow creating collections in transactions
        $collection->insertOne([]);

        $callback = static function (Session $session) use ($collection): void {
            $collection->insertOne([], ['session' => $session]);
        };

        $operation = new WithTransaction($callback);
        $session = $client->startSession();

        $this->setFixedJitter($operation, 0);
        $noBackoffTime = $this->runOperationWithTiming($operation, $session);

        $this->setFixedJitter($operation, 1);
        $withBackoffTime = $this->runOperationWithTiming($operation, $session);

        self::assertEqualsWithDelta($noBackoffTime + 1.8, $withBackoffTime, 0.5);
    }

    private function runOperationWithTiming(WithTransaction $operation, Session $session): float
    {
        $this->setUpCommitTransactionFailPoint();

        $start = microtime(true);
        $operation->execute($session);

        return microtime(true) - $start;
    }

    private function setFixedJitter(WithTransaction $operation, float $jitter): void
    {
        (new ReflectionProperty($operation, 'jitterGenerator'))
            ->setValue(
                $operation,
                static fn (): float => $jitter,
            );
    }

    private function setUpCommitTransactionFailPoint(): void
    {
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 13],
            'data' => [
                'failCommands' => ['commitTransaction'],
                'errorCode' => 251,
            ],
        ]);
    }
}
