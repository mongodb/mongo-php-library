<?php

namespace MongoDB\Tests\SpecTests\TransactionsConvenientApi;

use MongoDB\Driver\Session;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

use function MongoDB\with_transaction;
use function random_int;

/** @see https://github.com/mongodb/specifications/tree/master/source/transactions-convenient-api/tests#callback-returns-a-value */
class Prose2_ReturnValueTest extends FunctionalTestCase
{
    public function testCallbackReturnsCustomValue(): void
    {
        $this->markTestIncomplete('withTransaction does not return the callback return value (PHPLIB-994)');

        $this->skipIfTransactionsAreNotSupported();

        $value = random_int(0, 1_000_000);

        $callback = static fn (Session $session): int => $value;

        $client = self::createTestClient(static::getUri(true));

        $session = $client->startSession();

        $result = with_transaction($session, $callback);

        $this->assertSame($value, $result, 'withTransaction returns the callback return value');
    }
}
