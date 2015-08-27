<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Update;

class UpdateTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorFilterArgumentType($filter)
    {
        new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, array('$set' => array('x' => 1)));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidDocumentArguments
     */
    public function testConstructorUpdateArgumentType($update)
    {
        new Update($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), $update);
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidBooleanArguments
     */
    public function testConstructorMultiOptionType($multi)
    {
        new Update($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), array('y' => 1), array('multi' => $multi));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     * @dataProvider provideInvalidBooleanArguments
     */
    public function testConstructorUpsertOptionType($upsert)
    {
        new Update($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), array('y' => 1), array('upsert' => $upsert));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentTypeException
     */
    public function testConstructorWriteConcernOptionType()
    {
        new Update($this->getDatabaseName(), $this->getCollectionName(), array('x' => 1), array('y' => 1), array('writeConcern' => null));
    }
}
