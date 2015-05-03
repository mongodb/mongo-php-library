<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Driver\BulkWrite;

class BulkWriteFunctionalTest extends FunctionalTestCase
{
    private $omitModifiedCount;

    public function setUp()
    {
        parent::setUp();

        $this->omitModifiedCount = version_compare($this->getServerVersion(), '2.6.0', '<');
    }

    public function testInserts()
    {
        $ops = array(
            array('insertOne' => array(array('_id' => 1, 'x' => 11))),
            array('insertOne' => array(array('x' => 22))),
        );

        $result = $this->collection->bulkWrite($ops);
        $this->assertInstanceOf('MongoDB\BulkWriteResult', $result);
        $this->assertSame(2, $result->getInsertedCount());

        $insertedIds = $result->getInsertedIds();
        $this->assertSame(1, $insertedIds[0]);
        $this->assertInstanceOf('BSON\ObjectId', $insertedIds[1]);

        $expected = array(
            array('_id' => $insertedIds[0], 'x' => 11),
            array('_id' => $insertedIds[1], 'x' => 22),
        );

        $this->assertEquals($expected, $this->collection->find()->toArray());
    }

    public function testUpdates()
    {
        $this->createFixtures(4);

        $ops = array(
            array('updateOne' => array(array('_id' => 2), array('$inc' => array('x' => 1)))),
            array('updateMany' => array(array('_id' => array('$gt' => 2)), array('$inc' => array('x' => -1)))),
            array('updateOne' => array(array('_id' => 5), array('$set' => array('x' => 55)), array('upsert' => true))),
            array('updateOne' => array(array('x' => 66), array('$set' => array('x' => 66)), array('upsert' => true))),
            array('updateMany' => array(array('x' => array('$gt' => 50)), array('$inc' => array('x' => 1)))),
        );

        $result = $this->collection->bulkWrite($ops);
        $this->assertInstanceOf('MongoDB\BulkWriteResult', $result);
        $this->assertSame(5, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(5, $result->getModifiedCount());
        $this->assertSame(2, $result->getUpsertedCount());

        $upsertedIds = $result->getUpsertedIds();
        $this->assertSame(5, $upsertedIds[2]);
        $this->assertInstanceOf('BSON\ObjectId', $upsertedIds[3]);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 23),
            array('_id' => 3, 'x' => 32),
            array('_id' => 4, 'x' => 43),
            array('_id' => 5, 'x' => 56),
            array('_id' => $upsertedIds[3], 'x' => 67),
        );

        $this->assertEquals($expected, $this->collection->find()->toArray());
    }

    public function testDeletes()
    {
        $this->createFixtures(4);

        $ops = array(
            array('deleteOne' => array(array('_id' => 1))),
            array('deleteMany' => array(array('_id' => array('$gt' => 2)))),
        );

        $result = $this->collection->bulkWrite($ops);
        $this->assertInstanceOf('MongoDB\BulkWriteResult', $result);
        $this->assertSame(3, $result->getDeletedCount());

        $expected = array(
            array('_id' => 2, 'x' => 22),
        );

        $this->assertEquals($expected, $this->collection->find()->toArray());
    }

    public function testMixedOrderedOperations()
    {
        $this->createFixtures(3);

        $ops = array(
            array('updateOne' => array(array('_id' => array('$gt' => 1)), array('$inc' => array('x' => 1)))),
            array('updateMany' => array(array('_id' => array('$gt' => 1)), array('$inc' => array('x' => 1)))),
            array('insertOne' => array(array('_id' => 4, 'x' => 44))),
            array('deleteMany' => array(array('x' => array('$nin' => array(24, 34))))),
            array('replaceOne' => array(array('_id' => 4), array('_id' => 4, 'x' => 44), array('upsert' => true))),
        );

        $result = $this->collection->bulkWrite($ops);
        $this->assertInstanceOf('MongoDB\BulkWriteResult', $result);

        $this->assertSame(1, $result->getInsertedCount());
        $this->assertSame(array(2 => 4), $result->getInsertedIds());

        $this->assertSame(3, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(3, $result->getModifiedCount());
        $this->assertSame(1, $result->getUpsertedCount());
        $this->assertSame(array(4 => 4), $result->getUpsertedIds());

        $this->assertSame(2, $result->getDeletedCount());

        $expected = array(
            array('_id' => 2, 'x' => 24),
            array('_id' => 3, 'x' => 34),
            array('_id' => 4, 'x' => 44),
        );

        $this->assertEquals($expected, $this->collection->find()->toArray());
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unknown operation type called 'foo' (operation#0)
     */
    public function testUnknownOperation()
    {
        $this->collection->bulkWrite(array(
            array('foo' => array(array('_id' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessageRegExp /Missing argument#\d+ for '\w+' \(operation#\d+\)/
     * @dataProvider provideOpsWithMissingArguments
     */
    public function testMissingArguments(array $ops)
    {
        $this->collection->bulkWrite($ops);
    }

    public function provideOpsWithMissingArguments()
    {
        return array(
            array(array(array('insertOne' => array()))),
            array(array(array('updateOne' => array()))),
            array(array(array('updateOne' => array(array('_id' => 1))))),
            array(array(array('updateMany' => array()))),
            array(array(array('updateMany' => array(array('_id' => 1))))),
            array(array(array('replaceOne' => array()))),
            array(array(array('replaceOne' => array(array('_id' => 1))))),
            array(array(array('deleteOne' => array()))),
            array(array(array('deleteMany' => array()))),
        );
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage First key in $update must be a $operator
     */
    public function testUpdateOneRequiresUpdateOperators()
    {
        $this->collection->bulkWrite(array(
            array('updateOne' => array(array('_id' => 1), array('x' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage First key in $update must be a $operator
     */
    public function testUpdateManyRequiresUpdateOperators()
    {
        $this->collection->bulkWrite(array(
            array('updateMany' => array(array('_id' => array('$gt' => 1)), array('x' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage First key in $update must NOT be a $operator
     */
    public function testReplaceOneRequiresReplacementDocument()
    {
        $this->collection->bulkWrite(array(
            array('replaceOne' => array(array('_id' => 1), array('$inc' => array('x' => 1)))),
        ));
    }

    /**
     * Create data fixtures.
     *
     * @param integer $n
     */
    private function createFixtures($n)
    {
        $bulkWrite = new BulkWrite(true);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert(array(
                '_id' => $i,
                'x' => (integer) ($i . $i),
            ));
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}