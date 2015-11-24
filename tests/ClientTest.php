<?php

namespace MongoDB\Tests;

use MongoDB\Client;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

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

    public function testSelectCollectionInheritsReadPreferenceAndWriteConcern()
    {
        $clientOptions = [
            'readPreference' => 'secondaryPreferred',
            'w' => WriteConcern::MAJORITY,
        ];

        $client = new Client($this->getUri(), $clientOptions);
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $debug = $collection->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectCollectionPassesReadPreferenceAndWriteConcern()
    {
        $collectionOptions = [
            'readPreference' => new ReadPreference(ReadPreference::RP_SECONDARY_PREFERRED),
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $client = new Client($this->getUri());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName(), $collectionOptions);
        $debug = $collection->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectDatabaseInheritsReadPreferenceAndWriteConcern()
    {
        $clientOptions = [
            'readPreference' => 'secondaryPreferred',
            'w' => WriteConcern::MAJORITY,
        ];

        $client = new Client($this->getUri(), $clientOptions);
        $database = $client->selectDatabase($this->getDatabaseName());
        $debug = $database->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectDatabasePassesReadPreferenceAndWriteConcern()
    {
        $databaseOptions = [
            'readPreference' => new ReadPreference(ReadPreference::RP_SECONDARY_PREFERRED),
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $client = new Client($this->getUri());
        $database = $client->selectDatabase($this->getDatabaseName(), $databaseOptions);
        $debug = $database->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }
}
