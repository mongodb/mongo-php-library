<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
//use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
//use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\FindOneAndReplace;

class FindOneAndReplaceTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), $filter, []);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorReplacementArgumentTypeCheck($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], $replacement);
    }

    /** @dataProvider provideInvalidReplacementValues */
    public function testConstructorReplacementArgumentRequiresNoOperators($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $replacement argument is an update operator');
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], $replacement);
    }

    public function provideInvalidReplacementValues(): array
    {
        return [
            'update:array' => [['$set' => ['x' => 1]]],
            'update:object' => [(object) ['$set' => ['x' => 1]]],
            'update:Serializable' => [new BSONDocument(['$set' => ['x' => 1]])],
            'update:Document' => [Document::fromPHP(['$set' => ['x' => 1]])],
            // TODO: Enable the following tests when implementing PHPLIB-1129
            //'pipeline:array' => [[['$set' => ['x' => 1]]]],
            //'pipeline:Serializable' => [new BSONArray([['$set' => ['x' => 1]]])],
            //'pipeline:PackedArray' => [PackedArray::fromPHP([['$set' => ['x' => 1]]])],
            //'empty_pipeline:array' => [[]],
            //'empty_pipeline:Serializable' => [new BSONArray([])],
            //'empty_pipeline:PackedArray' => [PackedArray::fromPHP([])],
        ];
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], [], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['projection' => $value];
        }

        foreach ($this->getInvalidIntegerValues(true) as $value) {
            $options[][] = ['returnDocument' => $value];
        }

        return $options;
    }

    /** @dataProvider provideInvalidConstructorReturnDocumentOptions */
    public function testConstructorReturnDocumentOption($returnDocument): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), [], [], ['returnDocument' => $returnDocument]);
    }

    public function provideInvalidConstructorReturnDocumentOptions()
    {
        return $this->wrapValuesForDataProvider([-1, 0, 3]);
    }
}
