<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for findOneAndDelete().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class FindOneAndDeleteFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testFindOneAndDeleteWhenManyDocumentsMatch()
    {
        $filter = ['_id' => ['$gt' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndDelete($filter, $options);
        $this->assertSameDocument(['x' => 22], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndDeleteWhenOneDocumentMatches()
    {
        $filter = ['_id' => 2];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndDelete($filter, $options);
        $this->assertSameDocument(['x' => 22], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndDeleteWhenNoDocumentsMatch()
    {
        $filter = ['_id' => 4];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndDelete($filter, $options);
        $this->assertNull($document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
