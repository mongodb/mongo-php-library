<?php

namespace MongoDB\Tests;

use MongoDB\Collection;
use MongoDB\Driver\Manager;
use MongoDB\Model\IndexInfo;
use InvalidArgumentException;

class CollectionFunctionalTest extends FunctionalTestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getNamespace());
        $this->collection->deleteMany(array());
    }

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

    public function testCreateIndex()
    {
        $that = $this;

        $this->assertSame('x_1', $this->collection->createIndex(array('x' => 1), array('sparse' => true, 'unique' => true)));
        $this->assertIndexExists('x_1', function(IndexInfo $info) use ($that) {
            $that->assertTrue($info->isSparse());
            $that->assertTrue($info->isUnique());
            $that->assertFalse($info->isTtl());
        });

        $this->assertSame('y_-1_z_1', $this->collection->createIndex(array('y' => -1, 'z' => 1)));
        $this->assertIndexExists('y_-1_z_1', function(IndexInfo $info) use ($that) {
            $that->assertFalse($info->isSparse());
            $that->assertFalse($info->isUnique());
            $that->assertFalse($info->isTtl());
        });

        $this->assertSame('g_2dsphere_z_1', $this->collection->createIndex(array('g' => '2dsphere', 'z' => 1)));
        $this->assertIndexExists('g_2dsphere_z_1', function(IndexInfo $info) use ($that) {
            $that->assertFalse($info->isSparse());
            $that->assertFalse($info->isUnique());
            $that->assertFalse($info->isTtl());
        });

        $this->assertSame('t_1', $this->collection->createIndex(array('t' => 1), array('expireAfterSeconds' => 0)));
        $this->assertIndexExists('t_1', function(IndexInfo $info) use ($that) {
            $that->assertFalse($info->isSparse());
            $that->assertFalse($info->isUnique());
            $that->assertTrue($info->isTtl());
        });
    }

    public function testCreateIndexes()
    {
        $that = $this;

        $expectedNames = array('x_1', 'y_-1_z_1', 'g_2dsphere_z_1', 't_1');

        $indexes = array(
            array('key' => array('x' => 1), 'sparse' => true, 'unique' => true),
            array('key' => array('y' => -1, 'z' => 1)),
            array('key' => array('g' => '2dsphere', 'z' => 1)),
            array('key' => array('t' => 1), 'expireAfterSeconds' => 0),
        );

        $this->assertSame($expectedNames, $this->collection->createIndexes($indexes));

        $this->assertIndexExists('x_1', function(IndexInfo $info) use ($that) {
            $that->assertTrue($info->isSparse());
            $that->assertTrue($info->isUnique());
            $that->assertFalse($info->isTtl());
        });

        $this->assertIndexExists('y_-1_z_1', function(IndexInfo $info) use ($that) {
            $that->assertFalse($info->isSparse());
            $that->assertFalse($info->isUnique());
            $that->assertFalse($info->isTtl());
        });

        $this->assertIndexExists('g_2dsphere_z_1', function(IndexInfo $info) use ($that) {
            $that->assertFalse($info->isSparse());
            $that->assertFalse($info->isUnique());
            $that->assertFalse($info->isTtl());
        });

        $this->assertIndexExists('t_1', function(IndexInfo $info) use ($that) {
            $that->assertFalse($info->isSparse());
            $that->assertFalse($info->isUnique());
            $that->assertTrue($info->isTtl());
        });
    }

    public function testDropIndex()
    {
        $this->assertSame('x_1', $this->collection->createIndex(array('x' => 1)));
        $this->assertIndexExists('x_1');
        $this->assertCommandSucceeded($this->collection->dropIndex('x_1'));

        foreach ($this->collection->listIndexes() as $index) {
            if ($index->getName() === 'x_1') {
                $this->fail('The "x_1" index should have been deleted');
            }
        }
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testDropIndexShouldNotAllowWildcardCharacter()
    {
        $this->assertSame('x_1', $this->collection->createIndex(array('x' => 1)));
        $this->assertIndexExists('x_1');
        $this->collection->dropIndex('*');
    }

    public function testDropIndexes()
    {
        $this->assertSame('x_1', $this->collection->createIndex(array('x' => 1)));
        $this->assertSame('y_1', $this->collection->createIndex(array('y' => 1)));
        $this->assertIndexExists('x_1');
        $this->assertIndexExists('y_1');
        $this->assertCommandSucceeded($this->collection->dropIndexes());

        foreach ($this->collection->listIndexes() as $index) {
            if ($index->getName() === 'x_1') {
                $this->fail('The "x_1" index should have been deleted');
            }

            if ($index->getName() === 'y_1') {
                $this->fail('The "y_1" index should have been deleted');
            }
        }
    }

    public function testListIndexes()
    {
        $this->assertSame('x_1', $this->collection->createIndex(array('x' => 1)));

        $indexes = $this->collection->listIndexes();
        $this->assertInstanceOf('MongoDB\Model\IndexInfoIterator', $indexes);

        foreach ($indexes as $index) {
            $this->assertInstanceOf('MongoDB\Model\IndexInfo', $index);
        }
    }

    /**
     * Asserts that an index with the given name exists for the collection.
     *
     * An optional $callback may be provided, which should take an IndexInfo
     * argument as its first and only parameter. If an IndexInfo matching the
     * given name is found, it will be passed to the callback, which may perform
     * additional assertions.
     *
     * @param callable $callback
     */
    private function assertIndexExists($indexName, $callback = null)
    {
        if ($callback !== null && ! is_callable($callback)) {
            throw new InvalidArgumentException('$callback is not a callable');
        }

        $indexes = $this->collection->listIndexes();

        $foundIndex = null;

        foreach ($indexes as $index) {
            if ($index->getName() === $indexName) {
                $foundIndex = $index;
                break;
            }
        }

        $this->assertNotNull($foundIndex, sprintf('Found %s index for the collection', $indexName));

        if ($callback !== null) {
            call_user_func($callback, $foundIndex);
        }
    }
}
