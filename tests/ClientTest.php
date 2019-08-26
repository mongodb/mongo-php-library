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
        new Client(static::getUri(), [], $driverOptions);
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
        $client = new Client(static::getUri());

        $this->assertSame(static::getUri(), (string) $client);
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

        $client = new Client(static::getUri(), $uriOptions, $driverOptions);
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $debug = $collection->__debugInfo();

        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
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

        $client = new Client(static::getUri());
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName(), $collectionOptions);
        $debug = $collection->__debugInfo();

        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }

    public function testGetSelectsDatabaseAndInheritsOptions()
    {
        $uriOptions = ['w' => WriteConcern::MAJORITY];

        $client = new Client(static::getUri(), $uriOptions);
        $database = $client->{$this->getDatabaseName()};
        $debug = $database->__debugInfo();

        $this->assertSame($this->getDatabaseName(), $debug['databaseName']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
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

        $client = new Client(static::getUri(), $uriOptions, $driverOptions);
        $database = $client->selectDatabase($this->getDatabaseName());
        $debug = $database->__debugInfo();

        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
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

        $client = new Client(static::getUri());
        $database = $client->selectDatabase($this->getDatabaseName(), $databaseOptions);
        $debug = $database->__debugInfo();

        $this->assertInstanceOf(ReadConcern::class, $debug['readConcern']);
        $this->assertSame(ReadConcern::LOCAL, $debug['readConcern']->getLevel());
        $this->assertInstanceOf(ReadPreference::class, $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertIsArray($debug['typeMap']);
        $this->assertSame(['root' => 'array'], $debug['typeMap']);
        $this->assertInstanceOf(WriteConcern::class, $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }
}
