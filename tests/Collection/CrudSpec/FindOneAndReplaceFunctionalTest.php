<?php

namespace MongoDB\Tests\Collection\CrudSpec;

use MongoDB\Collection;
use MongoDB\Operation\FindOneAndReplace;

/**
 * CRUD spec functional tests for findOneAndReplace().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class FindOneAndReplaceFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testFindOneAndReplaceWhenManyDocumentsMatchReturningDocumentBeforeModification()
    {
        $filter = ['_id' => ['$gt' => 1]];
        $replacement = ['x' => 32];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertSameDocument(['x' => 22], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 32],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenManyDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = ['_id' => ['$gt' => 1]];
        $replacement = ['x' => 32];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
        ];

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertSameDocument(['x' => 32], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 32],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenOneDocumentMatchesReturningDocumentBeforeModification()
    {
        $filter = ['_id' => 2];
        $replacement = ['x' => 32];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertSameDocument(['x' => 22], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 32],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenOneDocumentMatchesReturningDocumentAfterModification()
    {
        $filter = ['_id' => 2];
        $replacement = ['x' => 32];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
        ];

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertSameDocument(['x' => 32], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 32],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenNoDocumentsMatchReturningDocumentBeforeModification()
    {
        $filter = ['_id' => 4];
        $replacement = ['x' => 44];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
        ];

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertNull($document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWithUpsertWhenNoDocumentsMatchReturningDocumentBeforeModification()
    {
        $filter = ['_id' => 4];
        // Server 2.4 and earlier requires any custom ID to also be in the replacement document
        $replacement = ['_id' => 4, 'x' => 44];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'upsert' => true,
        ];

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertNull($document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 4, 'x' => 44],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenNoDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = ['_id' => 4];
        $replacement = ['x' => 44];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
        ];

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertNull($document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWithUpsertWhenNoDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = ['_id' => 4];
        // Server 2.4 and earlier requires any custom ID to also be in the replacement document
        $replacement = ['_id' => 4, 'x' => 44];
        $options = [
            'projection' => ['x' => 1, '_id' => 0],
            'sort' => ['x' => 1],
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
            'upsert' => true,
        ];

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertSameDocument(['x' => 44], $document);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 4, 'x' => 44],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
