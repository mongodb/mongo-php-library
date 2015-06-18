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
        $filter = array('_id' => array('$gt' => 1));
        $replacement = array('x' => 32);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
        );

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertEquals((object) array('x' => 22), $document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 32),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenManyDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = array('_id' => array('$gt' => 1));
        $replacement = array('x' => 32);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
        );

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertEquals((object) array('x' => 32), $document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 32),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenOneDocumentMatchesReturningDocumentBeforeModification()
    {
        $filter = array('_id' => 2);
        $replacement = array('x' => 32);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
        );

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertEquals((object) array('x' => 22), $document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 32),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenOneDocumentMatchesReturningDocumentAfterModification()
    {
        $filter = array('_id' => 2);
        $replacement = array('x' => 32);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
        );

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertEquals((object) array('x' => 32), $document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 32),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenNoDocumentsMatchReturningDocumentBeforeModification()
    {
        $filter = array('_id' => 4);
        $replacement = array('x' => 44);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
        );

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertNull($document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWithUpsertWhenNoDocumentsMatchReturningDocumentBeforeModification()
    {
        $filter = array('_id' => 4);
        // Server 2.4 and earlier requires any custom ID to also be in the replacement document
        $replacement = array('_id' => 4, 'x' => 44);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
            'upsert' => true,
        );

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertNull($document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
            array('_id' => 4, 'x' => 44),
        );

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWhenNoDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = array('_id' => 4);
        $replacement = array('x' => 44);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
        );

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertNull($document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testFindOneAndReplaceWithUpsertWhenNoDocumentsMatchReturningDocumentAfterModification()
    {
        $filter = array('_id' => 4);
        // Server 2.4 and earlier requires any custom ID to also be in the replacement document
        $replacement = array('_id' => 4, 'x' => 44);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
            'returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER,
            'upsert' => true,
        );

        $document = $this->collection->findOneAndReplace($filter, $replacement, $options);
        $this->assertEquals((object) array('x' => 44), $document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
            array('_id' => 4, 'x' => 44),
        );

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
