<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Model\IndexInfo;
use InvalidArgumentException;

/**
 * Functional tests for index management methods.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/index-management.rst
 */
class IndexManagementFunctionalTest extends FunctionalTestCase
{
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

        $this->assertSame('my_ttl', $this->collection->createIndex(array('t' => 1), array('expireAfterSeconds' => 0, 'name' => 'my_ttl')));
        $this->assertIndexExists('my_ttl', function(IndexInfo $info) use ($that) {
            $that->assertFalse($info->isSparse());
            $that->assertFalse($info->isUnique());
            $that->assertTrue($info->isTtl());
        });
    }

    public function testCreateIndexes()
    {
        $that = $this;

        $expectedNames = array('x_1', 'y_-1_z_1', 'g_2dsphere_z_1', 'my_ttl');

        $indexes = array(
            array('key' => array('x' => 1), 'sparse' => true, 'unique' => true),
            array('key' => array('y' => -1, 'z' => 1)),
            array('key' => array('g' => '2dsphere', 'z' => 1)),
            array('key' => array('t' => 1), 'expireAfterSeconds' => 0, 'name' => 'my_ttl'),
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

        $this->assertIndexExists('my_ttl', function(IndexInfo $info) use ($that) {
            $that->assertFalse($info->isSparse());
            $that->assertFalse($info->isUnique());
            $that->assertTrue($info->isTtl());
        });
    }

    public function testCreateIndexesWithEmptyInputIsNop()
    {
        $this->assertSame(array(), $this->collection->createIndexes(array()));
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
    public function testDropIndexShouldNotAllowEmptyIndexName()
    {
        $this->assertSame('x_1', $this->collection->createIndex(array('x' => 1)));
        $this->assertIndexExists('x_1');
        $this->collection->dropIndex('');
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
