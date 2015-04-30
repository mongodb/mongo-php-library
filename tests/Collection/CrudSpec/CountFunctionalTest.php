<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for count().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class CountFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testCountWithoutFilter()
    {
        $this->assertSame(3, $this->collection->count());
    }

    public function testCountWithFilter()
    {
        $filter = array('_id' => array('$gt' => 1));

        $this->assertSame(2, $this->collection->count($filter));
    }

    public function testCountWithSkipAndLimit()
    {
        $filter = array();
        $options = array('skip' => 1, 'limit' => 3);

        $this->assertSame(2, $this->collection->count($filter, $options));
    }
}
