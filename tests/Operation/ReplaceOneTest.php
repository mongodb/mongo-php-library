<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
//use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
//use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\ReplaceOne;

class ReplaceOneTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), $filter, ['y' => 1]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorReplacementArgumentTypeCheck($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @doesNotPerformAssertions
     */
    public function testConstructorReplacementArgument($replacement): void
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    public function provideReplacementDocuments()
    {
        return $this->wrapValuesForDataProvider([
            ['y' => 1],
            (object) ['y' => 1],
            new BSONDocument(['y' => 1]),
        ]);
    }

    /** @dataProvider provideInvalidReplacementValues */
    public function testConstructorReplacementArgumentRequiresNoOperators($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $replacement argument is an update operator');
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
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
}
