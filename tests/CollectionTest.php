<?php
class CollectionTest extends PHPUnit_Framework_TestCase {

    function setUp() {
        require __DIR__ . "/" . "utils.inc";
        $this->faker = Faker\Factory::create();
        $this->faker->seed(1234);

        $this->manager = new MongoDB\Manager("mongodb://localhost");
        $this->collection = new MongoDB\Collection($this->manager, "test.case");
        $this->collection->deleteMany(array());
    }

    function testInsertAndRetrieve() {
        $collection = $this->collection;

        for($i=0; $i<10;$i++) {
            $user = createUser($this->faker);
            $result = $collection->insertOne($user);
            $this->assertInstanceOf("MongoDB\\InsertResult", $result);
            $this->assertInstanceOf("BSON\ObjectId", $result->getInsertedId());
            $this->assertEquals(24, strlen($result->getInsertedId()));

            $user["_id"] = $result->getInsertedId();
            $document = $collection->findOne(array("_id" => $result->getInsertedId()));
            $this->assertEquals($document, $user, "The inserted and returned objects are the same");
        }

        $this->assertEquals(10, $i);


        $query = array("firstName" => "Ransom");
        $count = $collection->count($query);
        $this->assertEquals(1, $count);
        $cursor = $collection->find($query);
        $this->assertInstanceOf("MongoDB\\QueryResult", $cursor);

        foreach($cursor as $n => $person) {
            $this->assertInternalType("array", $person);
        }
        $this->assertEquals(0, $n);
    }
}

