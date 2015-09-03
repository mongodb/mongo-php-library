<?php

namespace MongoDB\Tests\Database;

use MongoDB\Database;

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
        return array(
            array(null),
            array(''),
        );
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
        $writeResult = $this->manager->executeInsert($this->getNamespace(), array('x' => 1));
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->database->drop();
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }
}
