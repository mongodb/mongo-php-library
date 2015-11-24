<?php

namespace MongoDB\Tests;

use MongoDB\Client;

/**
 * Unit tests for the Client class.
 */
class ClientTest extends TestCase
{
    public function testConstructorDefaultUri()
    {
        $client = new Client();

        $this->assertEquals('mongodb://localhost:27017', (string) $client);
    }

    public function testToString()
    {
        $client = new Client($this->getUri());

        $this->assertSame($this->getUri(), (string) $client);
    }
}
