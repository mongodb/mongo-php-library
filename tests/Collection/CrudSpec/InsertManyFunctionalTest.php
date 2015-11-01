<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for insertMany().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class InsertManyFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(1);
    }

    public function testInsertManyWithNonexistentDocuments()
    {
        $documents = [
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $result = $this->collection->insertMany($documents);
        $this->assertSame(2, $result->getInsertedCount());
        $this->assertSame([2, 3], $result->getInsertedIds());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
