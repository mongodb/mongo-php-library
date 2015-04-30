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
        $filter = array('_id' => array('$gt' => 1));
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
        );

        $document = $this->collection->findOneAndDelete($filter, $options);
        $this->assertSame(array('x' => 22), $document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testFindOneAndDeleteWhenOneDocumentMatches()
    {
        $filter = array('_id' => 2);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
        );

        $document = $this->collection->findOneAndDelete($filter, $options);
        $this->assertSame(array('x' => 22), $document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testFindOneAndDeleteWhenNoDocumentsMatch()
    {
        $filter = array('_id' => 4);
        $options = array(
            'projection' => array('x' => 1, '_id' => 0),
            'sort' => array('x' => 1),
        );

        $document = $this->collection->findOneAndDelete($filter, $options);
        $this->assertNull($document);

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }
}
