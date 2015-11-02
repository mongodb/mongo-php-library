<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for deleteOne().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class DeleteOneFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testDeleteOneWhenManyDocumentsMatch()
    {
        $filter = ['_id' => ['$gt' => 1]];

        $result = $this->collection->deleteOne($filter);
        $this->assertSame(1, $result->getDeletedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testDeleteOneWhenOneDocumentMatches()
    {
        $filter = ['_id' => 2];

        $result = $this->collection->deleteOne($filter);
        $this->assertSame(1, $result->getDeletedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testDeleteOneWhenNoDocumentsMatch()
    {
        $filter = ['_id' => 4];

        $result = $this->collection->deleteOne($filter);
        $this->assertSame(0, $result->getDeletedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
