<?php

namespace MongoDB\Tests\Collection\CrudSpec;

use MongoDB\Collection;
use MongoDB\Operation\FindOneAndUpdate;

/**
 * CRUD spec functional tests for findOneAndUpdate().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class FindOneAndUpdateFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testFindOneAndUpdateWhenManyDocumentsMatchReturningDocumentBeforeModification()
    {
        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndUpdate($filter, $update, $options);
        $this->assertSameDocument(['x' => 22], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndUpdateWhenManyDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
        ];

        $document = $this->collection->findOneAndUpdate($filter, $update, $options);
        $this->assertSameDocument(['x' => 23], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndUpdateWhenOneDocumentMatchesReturningDocumentBeforeModification()
    {
        $filter = ['_id' => 2];
        $update = ['$inc' => ['x' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndUpdate($filter, $update, $options);
        $this->assertSameDocument(['x' => 22], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndUpdateWhenOneDocumentMatchesReturningDocumentAfterModification()
    {
        $filter = ['_id' => 2];
        $update = ['$inc' => ['x' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
        ];

        $document = $this->collection->findOneAndUpdate($filter, $update, $options);
        $this->assertSameDocument(['x' => 23], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndUpdateWhenNoDocumentsMatchReturningDocumentBeforeModification()
    {
        $filter = ['_id' => 4];
        $update = ['$inc' => ['x' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndUpdate($filter, $update, $options);
        $this->assertNull($document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndUpdateWithUpsertWhenNoDocumentsMatchReturningDocumentBeforeModification()
    {
        $filter = ['_id' => 4];
        $update = ['$inc' => ['x' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'upsert' => true,
        ];

        $document = $this->collection->findOneAndUpdate($filter, $update, $options);
        $this->assertNull($document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 4, 'x' => 1],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndUpdateWhenNoDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = ['_id' => 4];
        $update = ['$inc' => ['x' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
        ];

        $document = $this->collection->findOneAndUpdate($filter, $update, $options);
        $this->assertNull($document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndUpdateWithUpsertWhenNoDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = ['_id' => 4];
        $update = ['$inc' => ['x' => 1]];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
            'upsert' => true,
        ];

        $document = $this->collection->findOneAndUpdate($filter, $update, $options);
        $this->assertSameDocument(['x' => 1], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 4, 'x' => 1],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
