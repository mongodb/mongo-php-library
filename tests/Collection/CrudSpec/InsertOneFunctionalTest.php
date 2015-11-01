<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for insertOne().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class InsertOneFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(1);
    }

    public function testInsertOneWithANonexistentDocument()
    {
        $document = ['_id' => 2, 'x' => 22];

        $result = $this->collection->insertOne($document);
        $this->assertSame(1, $result->getInsertedCount());
        $this->assertSame(2, $result->getInsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
