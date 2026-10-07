<?php

namespace MongoDB\Tests\SpecTests\TransactionsConvenientApi;

use MongoDB\Driver\Session;
use MongoDB\Tests\SpecTests\FunctionalTestCase;
use RuntimeException;

use function MongoDB\with_transaction;

/** @see https://github.com/mongodb/specifications/tree/master/source/transactions-convenient-api/tests#callback-raises-a-custom-error */
class Prose1_CallbackRaisesCustomErrorTest extends FunctionalTestCase
{
    public function testCallbackRaisesCustomError(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        $attempts = 0;

        $callback = static function (Session $session) use (&$attempts): void {
            $attempts++;

            throw new RuntimeException('Custom error');
        };

        $client = self::createTestClient(static::getUri(true));

        $session = $client->startSession();

        try {
            with_transaction($session, $callback);
            $this->fail('Expected RuntimeException was not thrown');
        } catch (RuntimeException $e) {
            $this->assertSame('Custom error', $e->getMessage());
        }

        $this->assertSame(1, $attempts);
    }
}
