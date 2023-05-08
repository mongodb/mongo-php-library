<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\UpdateMany;

class UpdateManyTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UpdateMany($this->getDatabaseName(), $this->getCollectionName(), $filter, ['$set' => ['x' => 1]]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UpdateMany($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    /**
     * @dataProvider provideUpdateDocuments
     * @doesNotPerformAssertions
     */
    public function testConstructorUpdateArgument($update): void
    {
        new UpdateMany($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    public function provideUpdateDocuments()
    {
        return $this->wrapValuesForDataProvider([
            ['$set' => ['y' => 1]],
            (object) ['$set' => ['y' => 1]],
            new BSONDocument(['$set' => ['y' => 1]]),
        ]);
    }

    /** @dataProvider provideInvalidUpdateValues */
    public function testConstructorUpdateArgumentRequiresOperators($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an update document with operator as first key or a pipeline');
        new UpdateMany($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    public function provideInvalidUpdateValues(): array
    {
        return [
            'replacement:array' => [['x' => 1]],
            'replacement:object' => [(object) ['x' => 1]],
            'replacement:Serializable' => [new BSONDocument(['x' => 1])],
            'replacement:Document' => [Document::fromPHP(['x' => 1])],
            'empty_pipeline:array' => [[]],
            'empty_pipeline:Serializable' => [new BSONArray([])],
            'empty_pipeline:PackedArray' => [PackedArray::fromPHP([])],
        ];
    }
}
