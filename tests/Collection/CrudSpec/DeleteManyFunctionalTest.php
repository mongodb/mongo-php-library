<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for deleteMany().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class DeleteManyFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testDeleteManyWhenManyDocumentsMatch()
    {
        $filter = array('_id' => array('$gt' => 1));

        $result = $this->collection->deleteMany($filter);
        $this->assertSame(2, $result->getDeletedCount());

        $expected = array(
            array('_id' => 1, 'x' => 11),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testDeleteManyWhenNoDocumentsMatch()
    {
        $filter = array('_id' => 4);

        $result = $this->collection->deleteMany($filter);
        $this->assertSame(0, $result->getDeletedCount());

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }
}
