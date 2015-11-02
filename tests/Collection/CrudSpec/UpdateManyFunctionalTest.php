<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for updateMany().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class UpdateManyFunctionalTest extends FunctionalTestCase
{
    private $omitModifiedCount;

    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);

        $this->omitModifiedCount = version_compare($this->getServerVersion(), '2.6.0', '<');
    }

    public function testUpdateManyWhenManyDocumentsMatch()
    {
        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];

        $result = $this->collection->updateMany($filter, $update);
        $this->assertSame(2, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(2, $result->getModifiedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 34],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateManyWhenOneDocumentMatches()
    {
        $filter = ['_id' => 1];
        $update = ['$inc' => ['x' => 1]];

        $result = $this->collection->updateMany($filter, $update);
        $this->assertSame(1, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(1, $result->getModifiedCount());

        $expected = [
            ['_id' => 1, 'x' => 12],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateManyWhenNoDocumentsMatch()
    {
        $filter = ['_id' => 4];
        $update = ['$inc' => ['x' => 1]];

        $result = $this->collection->updateMany($filter, $update);
        $this->assertSame(0, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(0, $result->getModifiedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateManyWithUpsertWhenNoDocumentsMatch()
    {
        $filter = ['_id' => 4];
        $update = ['$inc' => ['x' => 1]];
        $options = ['upsert' => true];

        $result = $this->collection->updateMany($filter, $update, $options);
        $this->assertSame(0, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(0, $result->getModifiedCount());
        $this->assertSame(4, $result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 4, 'x' => 1],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
