<?php

use MongoDB\Collection;
use MongoDB\Driver\Manager;

class CollectionTest extends PHPUnit_Framework_TestCase {

    function setUp() {
        require_once __DIR__ . "/" . "utils.inc";
        $this->faker = Faker\Factory::create();
        $this->faker->seed(1234);

        $this->manager = new Manager("mongodb://localhost");
        $this->collection = new Collection($this->manager, "test.case");
        $this->collection->deleteMany(array());
    }

    function testInsertAndRetrieve() {
        $collection = $this->collection;

        for($i=0; $i<10;$i++) {
            $user = createUser($this->faker);
            $result = $collection->insertOne($user);
            $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
            $this->assertInstanceOf('BSON\ObjectId', $result->getInsertedId());
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
        $this->assertInstanceOf('MongoDB\Driver\Result', $cursor);

        foreach($cursor as $n => $person) {
            $this->assertInternalType("array", $person);
        }
        $this->assertEquals(0, $n);
    }

    public function testMethodOrder()
    {
        $class = new ReflectionClass('MongoDB\Collection');

        $filters = array(
            'public' => ReflectionMethod::IS_PUBLIC,
            'protected' => ReflectionMethod::IS_PROTECTED,
            'private' => ReflectionMethod::IS_PRIVATE,
        );

        foreach ($filters as $visibility => $filter) {
            $methods = array_map(
                function(ReflectionMethod $method) { return $method->getName(); },
                $class->getMethods($filter)
            );

            $sortedMethods = $methods;
            sort($sortedMethods);

            $this->assertEquals($methods, $sortedMethods, sprintf('%s methods are declared alphabetically', ucfirst($visibility)));
        }
    }
}

