<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\BulkWrite;

class BulkWriteTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $operations is empty
     */
    public function testOperationsMustNotBeEmpty()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array());
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage $operations is not a list (unexpected index: "1")
     */
    public function testOperationsMustBeAList()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            1 => array(BulkWrite::INSERT_ONE => array(array('x' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected one element in $operation[0], actually: 2
     */
    public function testMultipleOperationsInOneElement()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(
                BulkWrite::INSERT_ONE => array(array('x' => 1)),
                BulkWrite::DELETE_ONE => array(array('x' => 1)),
            ),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unknown operation type "foo" in $operations[0]
     */
    public function testUnknownOperation()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array('foo' => array(array('_id' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing first argument for $operations[0]["insertOne"]
     */
    public function testInsertOneDocumentArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::INSERT_ONE => array()),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["insertOne"\]\[0\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testInsertOneDocumentArgumentTypeCheck($document)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::INSERT_ONE => array($document)),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing first argument for $operations[0]["deleteMany"]
     */
    public function testDeleteManyFilterArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::DELETE_MANY => array()),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["deleteMany"\]\[0\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testDeleteManyFilterArgumentTypeCheck($document)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::DELETE_MANY => array($document)),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing first argument for $operations[0]["deleteOne"]
     */
    public function testDeleteOneFilterArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::DELETE_ONE => array()),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["deleteOne"\]\[0\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testDeleteOneFilterArgumentTypeCheck($document)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::DELETE_ONE => array($document)),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing first argument for $operations[0]["replaceOne"]
     */
    public function testReplaceOneFilterArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::REPLACE_ONE => array()),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["replaceOne"\]\[0\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testReplaceOneFilterArgumentTypeCheck($filter)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::REPLACE_ONE => array($filter, array('y' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing second argument for $operations[0]["replaceOne"]
     */
    public function testReplaceOneReplacementArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::REPLACE_ONE => array(array('x' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["replaceOne"\]\[1\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testReplaceOneReplacementArgumentTypeCheck($replacement)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::REPLACE_ONE => array(array('x' => 1), $replacement)),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage First key in $operations[0]["replaceOne"][1] is an update operator
     */
    public function testReplaceOneReplacementArgumentRequiresNoOperators()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::REPLACE_ONE => array(array('_id' => 1), array('$inc' => array('x' => 1)))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["replaceOne"\]\[2\]\["upsert"\] to have type "boolean" but found "[\w ]+"/
     * @dataProvider provideInvalidBooleanValues
     */
    public function testReplaceOneUpsertOptionTypeCheck($upsert)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::REPLACE_ONE => array(array('x' => 1), array('y' => 1), array('upsert' => $upsert))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing first argument for $operations[0]["updateMany"]
     */
    public function testUpdateManyFilterArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_MANY => array()),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["updateMany"\]\[0\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateManyFilterArgumentTypeCheck($filter)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_MANY => array($filter, array('$set' => array('x' => 1)))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing second argument for $operations[0]["updateMany"]
     */
    public function testUpdateManyUpdateArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_MANY => array(array('x' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["updateMany"\]\[1\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateManyUpdateArgumentTypeCheck($update)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_MANY => array(array('x' => 1), $update)),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage First key in $operations[0]["updateMany"][1] is not an update operator
     */
    public function testUpdateManyUpdateArgumentRequiresOperators()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_MANY => array(array('_id' => array('$gt' => 1)), array('x' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["updateMany"\]\[2\]\["upsert"\] to have type "boolean" but found "[\w ]+"/
     * @dataProvider provideInvalidBooleanValues
     */
    public function testUpdateManyUpsertOptionTypeCheck($upsert)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_MANY => array(array('x' => 1), array('$set' => array('x' => 1)), array('upsert' => $upsert))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing first argument for $operations[0]["updateOne"]
     */
    public function testUpdateOneFilterArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_ONE => array()),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["updateOne"\]\[0\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateOneFilterArgumentTypeCheck($filter)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_ONE => array($filter, array('$set' => array('x' => 1)))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing second argument for $operations[0]["updateOne"]
     */
    public function testUpdateOneUpdateArgumentMissing()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_ONE => array(array('x' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["updateOne"\]\[1\] to have type "array or object" but found "[\w ]+"/
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateOneUpdateArgumentTypeCheck($update)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_ONE => array(array('x' => 1), $update)),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @expectedExceptionMessage First key in $operations[0]["updateOne"][1] is not an update operator
     */
    public function testUpdateOneUpdateArgumentRequiresOperators()
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_ONE => array(array('_id' => 1), array('x' => 1))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @expectedExceptionMessageRegExp /Expected \$operations\[0\]\["updateOne"\]\[2\]\["upsert"\] to have type "boolean" but found "[\w ]+"/
     * @dataProvider provideInvalidBooleanValues
     */
    public function testUpdateOneUpsertOptionTypeCheck($upsert)
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), array(
            array(BulkWrite::UPDATE_ONE => array(array('x' => 1), array('$set' => array('x' => 1)), array('upsert' => $upsert))),
        ));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        new BulkWrite(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            array(array(BulkWrite::INSERT_ONE => array(array('x' => 1)))),
            $options
        );
    }

    public function provideInvalidConstructorOptions()
    {
        $options = array();

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = array('ordered' => $value);
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = array('writeConcern' => $value);
        }

        return $options;
    }
}
