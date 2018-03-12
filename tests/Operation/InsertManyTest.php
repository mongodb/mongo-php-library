<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\InsertMany;

class InsertManyTest extends TestCase
{
    public function testConstructorDocumentsMustNotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$documents is empty');
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    public function testConstructorDocumentsMustBeAList()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$documents is not a list (unexpected index: "1")');
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [1 => ['x' => 1]]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorDocumentsArgumentElementTypeChecks($document)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$documents[0\] to have type "array or object" but found "[\w ]+"/');
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [$document]);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [['x' => 1]], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['bypassDocumentValidation' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['ordered' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }
}
