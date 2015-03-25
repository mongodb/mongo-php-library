<?php

namespace MongoDB\Tests;

use MongoDB\Collection;
use MongoDB\Driver\Manager;

class CollectionFunctionalTest extends FunctionalTestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getNamespace());
        $this->collection->deleteMany(array());
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
        $this->assertInstanceOf('MongoDB\Driver\Result', $cursor);

        foreach($cursor as $n => $person) {
            $this->assertInternalType("array", $person);
        }
        $this->assertEquals(0, $n);
    }
}
