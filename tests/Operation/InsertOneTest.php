<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\InsertOne;

class InsertOneTest extends TestCase
{
    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorDocumentArgumentTypeCheck($document)
    {
        $this->expectException(InvalidArgumentException::class);
        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['bypassDocumentValidation' => $value];
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
