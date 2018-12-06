<?php

namespace MongoDB\Tests;

use MongoDB\Client;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;

/**
 * Unit tests for the Client class.
 */
class ClientTest extends TestCase
{
    public function testConstructorDefaultUri()
    {
        $client = new Client();

        $this->assertEquals('mongodb://127.0.0.1/', (string) $client);
    }

    /**
     * @dataProvider provideInvalidConstructorDriverOptions
     */
    public function testConstructorDriverOptionTypeChecks(array $driverOptions)
    {
        $this->expectException(InvalidArgumentException::class);
        new Client($this->getUri(), [], $driverOptions);
    }

    public function provideInvalidConstructorDriverOptions()
    {
        $options = [];

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        return $options;
    }

    public function testToString()
    {
        $client = new Client($this->getUri());

        $this->assertSame($this->getUri(), (string) $client);
    }

    public function testSelectCollectionInheritsOptions()
    {
        $uriOptions = [
            'readConcernLevel' => ReadConcern::LOCAL,
            'readPreference' => 'secondaryPreferred',
            'w' => WriteConcern::MAJORITY,
        ];

        $driverOptions = [
            'typeMap' => ['root' => 'array'],
        ];

        $client = new Client($this->getUri(), $uriOptions, $driverOptions);
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $debug = $collection->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadConcern', $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInternalType('array', $debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectCollectionPassesOptions()
    {
        $collectionOptions = [
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::RP_SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $client = new Client($this->getUri());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName(), $collectionOptions);
        $debug = $collection->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadConcern', $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInternalType('array', $debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testGetSelectsDatabaseAndInheritsOptions()
    {
        $uriOptions = ['w' => WriteConcern::MAJORITY];

        $client = new Client($this->getUri(), $uriOptions);
        $database = $client->{$this->getDatabaseName()};
        $debug = $database->__debugInfo();

        $this->assertSame($this->getDatabaseName(), $debug['databaseName']);
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectDatabaseInheritsOptions()
    {
        $uriOptions = [
            'readConcernLevel' => ReadConcern::LOCAL,
            'readPreference' => 'secondaryPreferred',
            'w' => WriteConcern::MAJORITY,
        ];

        $driverOptions = [
            'typeMap' => ['root' => 'array'],
        ];

        $client = new Client($this->getUri(), $uriOptions, $driverOptions);
        $database = $client->selectDatabase($this->getDatabaseName());
        $debug = $database->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadConcern', $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInternalType('array', $debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testSelectDatabasePassesOptions()
    {
        $databaseOptions = [
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'readPreference' => new ReadPreference(ReadPreference::RP_SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $client = new Client($this->getUri());
        $database = $client->selectDatabase($this->getDatabaseName(), $databaseOptions);
        $debug = $database->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadConcern', $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInternalType('array', $debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testWithOptionsAllowsOverridingDatabaseOptions()
    {
        $originalUriOptions = [
            'readConcernLevel' => ReadConcern::LOCAL,
            'readPreference' => 'secondaryPreferred',
        ];

        $originalDriverOptions = [
            'typeMap' => ['root' => 'array'],
        ];

        $client = new Client($this->getUri(), $originalUriOptions, $originalDriverOptions);

        $this->assertInstanceOf('MongoDB\Driver\ReadConcern', $client->getReadConcern());
        $this->assertSame(ReadConcern::LOCAL, $client-> getReadConcern()->getLevel());
        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $client->getReadPreference());
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $client->getReadPreference()->getMode());
        $this->assertInternalType('array', $client->getTypeMap());
        $this->assertSame(['root' => 'array'], $client->getTypeMap());

        $changedOptions = [
            'readConcern' => new ReadConcern(ReadConcern::MAJORITY),
            'readPreference' => new ReadPreference(ReadPreference::RP_NEAREST, [['dc' => 'ny']]),
            'typeMap' => ['array' => 'array'],
            'writeConcern' => new WriteConcern(3, 1000, true),
        ];

        $changedClient = $client->withOptions($changedOptions);

        $this->assertInstanceOf('MongoDB\Driver\ReadConcern', $changedClient->getReadConcern());
        $this->assertSame(ReadConcern::MAJORITY, $changedClient->getReadConcern()->getLevel());
        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $changedClient->getReadPreference());
        $this->assertSame(ReadPreference::RP_NEAREST, $changedClient->getReadPreference()->getMode());
        $this->assertSame([['dc' => 'ny']], $changedClient->getReadPreference()->getTagSets());
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $changedClient->getWriteConcern());
        $this->assertSame(3, $changedClient->getWriteConcern()->getW());
        $this->assertSame(1000, $changedClient->getWriteConcern()->getWtimeout());
        $this->assertSame(true, $changedClient->getWriteConcern()->getJournal());
        $this->assertInternalType('array', $changedClient->getTypeMap());
        $this->assertSame(['array' => 'array'], $changedClient->getTypeMap());

        $this->assertEquals($changedClient->getReadConcern(), $changedClient->getManager()->getReadConcern());
        $this->assertEquals($changedClient->getReadPreference(), $changedClient->getManager()->getReadPreference());
        $this->assertEquals($changedClient->getWriteConcern(), $changedClient->getManager()->getWriteConcern());
    }
}
