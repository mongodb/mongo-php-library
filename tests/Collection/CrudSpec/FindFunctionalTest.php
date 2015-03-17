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
        $filter = array('_id' => 1);

        $expected = array(
            array('_id' => 1, 'x' => 11),
        );

        $this->assertSame($expected, $this->collection->find($filter)->toArray());
    }

    public function testFindWithFilterSortSkipAndLimit()
    {
        $filter = array('_id' => array('$gt' => 2));
        $options = array(
            'sort' => array('_id' => 1),
            'skip' => 2,
            'limit' => 2,
        );

        $expected = array(
            array('_id' => 5, 'x' => 55),
        );

        $this->assertSame($expected, $this->collection->find($filter, $options)->toArray());
    }

    public function testFindWithLimitSortAndBatchSize()
    {
        $filter = array();
        $options = array(
            'sort' => array('_id' => 1),
            'limit' => 4,
            'batchSize' => 2,
        );

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
            array('_id' => 4, 'x' => 44),
        );

        $this->assertSame($expected, $this->collection->find($filter, $options)->toArray());
    }
}
