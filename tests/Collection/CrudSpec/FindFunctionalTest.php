<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for find().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class FindFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(5);
    }

    public function testFindWithFilter()
    {
        $filter = ['_id' => 1];

        $expected = [
            ['_id' => 1, 'x' => 11],
        ];

        $this->assertSameDocuments($expected, $this->collection->find($filter));
    }

    public function testFindWithFilterSortSkipAndLimit()
    {
        $filter = ['_id' => ['$gt' => 2]];
        $options = [
            'sort' => ['_id' => 1],
            'skip' => 2,
            'limit' => 2,
        ];

        $expected = [
            ['_id' => 5, 'x' => 55],
        ];

        $this->assertSameDocuments($expected, $this->collection->find($filter, $options));
    }

    public function testFindWithLimitSortAndBatchSize()
    {
        $filter = [];
        $options = [
            'sort' => ['_id' => 1],
            'limit' => 4,
            'batchSize' => 2,
        ];

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 4, 'x' => 44],
        ];

        $this->assertSameDocuments($expected, $this->collection->find($filter, $options));
    }
}
