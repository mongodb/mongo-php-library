<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Collection;
use MongoDB\InsertOneResult;
use MongoDB\Driver\WriteConcern;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\InsertOne;

class InsertOneFunctionalTest extends FunctionalTestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    /**
     * @dataProvider provideDocumentWithExistingId
     */
    public function testInsertOneWithExistingId($document)
    {
        $operation = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());
        $this->assertSame('foo', $result->getInsertedId());

        $expected = [
            ['_id' => 'foo', 'x' => 11],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function provideDocumentWithExistingId()
    {
        return [
            [['_id' => 'foo', 'x' => 11]],
            [(object) ['_id' => 'foo', 'x' => 11]],
            [new BSONDocument(['_id' => 'foo', 'x' => 11])],
        ];
    }

    public function testInsertOneWithGeneratedId()
    {
        $document = ['x' => 11];

        $operation = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());
        $this->assertInstanceOf('MongoDB\BSON\ObjectId', $result->getInsertedId());

        $expected = [
            ['_id' => $result->getInsertedId(), 'x' => 11],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUnacknowledgedWriteConcern()
    {
        $document = ['x' => 11];
        $options = ['writeConcern' => new WriteConcern(0)];

        $operation = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($result->isAcknowledged());

        return $result;
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessageRegExp /[\w:\\]+ should not be called for an unacknowledged write result/
     */
    public function testUnacknowledgedWriteConcernAccessesInsertedCount(InsertOneResult $result)
    {
        $result->getInsertedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesInsertedId(InsertOneResult $result)
    {
        $this->assertInstanceOf('MongoDB\BSON\ObjectId', $result->getInsertedId());
    }
}
