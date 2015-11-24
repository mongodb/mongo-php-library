<?php

namespace MongoDB\Tests\Database;

use MongoDB\Database;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

/**
 * Functional tests for the Database class.
 */
class DatabaseFunctionalTest extends FunctionalTestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidDatabaseValues
     */
    public function testConstructorDatabaseNameArgument($databaseName)
    {
        // TODO: Move to unit test once ManagerInterface can be mocked (PHPC-378)
        new Database($this->manager, $databaseName);
    }

    public function provideInvalidDatabaseValues()
    {
        return [
            [null],
            [''],
        ];
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new Database($this->manager, $this->getDatabaseName(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }

    public function testToString()
    {
        $this->assertEquals($this->getDatabaseName(), (string) $this->database);
    }

    public function getGetDatabaseName()
    {
        $this->assertEquals($this->getDatabaseName(), $this->database->getDatabaseName());
    }

    public function testDrop()
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->insert(['x' => 1]);

        $writeResult = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->database->drop();
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    public function testSelectCollectionInheritsReadPreferenceAndWriteConcern()
    {
        $databaseOptions = [
            'readPreference' => new ReadPreference(ReadPreference::RP_SECONDARY_PREFERRED),
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $database = new Database($this->manager, $this->getDatabaseName(), $databaseOptions);
        $collection = $database->selectCollection($this->getCollectionName());
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

        $collection = $this->database->selectCollection($this->getCollectionName(), $collectionOptions);
        $debug = $collection->__debugInfo();

        $this->assertInstanceOf('MongoDB\Driver\ReadPreference', $debug['readPreference']);
        $this->assertSame(ReadPreference::RP_SECONDARY_PREFERRED, $debug['readPreference']->getMode());
        $this->assertInstanceOf('MongoDB\Driver\WriteConcern', $debug['writeConcern']);
        $this->assertSame(WriteConcern::MAJORITY, $debug['writeConcern']->getW());
    }
}
