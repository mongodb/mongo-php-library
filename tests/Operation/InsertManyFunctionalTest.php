<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Operation\InsertMany;

class InsertManyFunctionalTest extends FunctionalTestCase
{
    public function testInsertMany()
    {
        $documents = [
            ['_id' => 'foo', 'x' => 11],
            ['x' => 22],
            ['_id' => 'bar', 'x' => 22],
        ];

        $operation = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), $documents);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf('MongoDB\InsertManyResult', $result);
        $this->assertSame(3, $result->getInsertedCount());

        $insertedIds = $result->getInsertedIds();
        $this->assertSame('foo', $insertedIds[0]);
        $this->assertInstanceOf('MongoDB\BSON\ObjectId', $insertedIds[1]);
        $this->assertSame('bar', $insertedIds[2]);

        $expected = [
            ['_id' => 'foo', 'x' => 11],
            ['_id' => $insertedIds[1], 'x' => 22],
            ['_id' => 'bar', 'x' => 22],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
