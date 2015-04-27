<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Tests\FixtureGenerator;

/**
 * Functional tests for the Collection class.
 */
class CollectionFunctionalTest extends FunctionalTestCase
{
    public function testDrop()
    {
        $writeResult = $this->collection->insertOne(array('x' => 1));
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->collection->drop();
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    function testInsertAndRetrieve()
    {
        $generator = new FixtureGenerator();

        for ($i = 0; $i < 10; $i++) {
            $user = $generator->createUser();
            $result = $this->collection->insertOne($user);
            $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
            $this->assertInstanceOf('BSON\ObjectId', $result->getInsertedId());
            $this->assertEquals(24, strlen($result->getInsertedId()));

            $user["_id"] = $result->getInsertedId();
            $document = $this->collection->findOne(array("_id" => $result->getInsertedId()));
            $this->assertEquals($document, $user, "The inserted and returned objects are the same");
        }

        $this->assertEquals(10, $i);

        $query = array("firstName" => "Ransom");
        $count = $this->collection->count($query);
        $this->assertEquals(1, $count);
        $cursor = $this->collection->find($query);
        $this->assertInstanceOf('MongoDB\Driver\Cursor', $cursor);

        foreach($cursor as $n => $person) {
            $this->assertInternalType("array", $person);
        }
        $this->assertEquals(0, $n);
    }
}
