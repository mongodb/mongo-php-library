<?php

namespace MongoDB\Tests;

use MongoDB\Client;

/**
 * Unit tests for the Client class.
 */
class ClientTest extends TestCase
{
    public function testToString()
    {
        $client = new Client($this->getUri());

        $this->assertSame($this->getUri(), (string) $client);
    }
}
