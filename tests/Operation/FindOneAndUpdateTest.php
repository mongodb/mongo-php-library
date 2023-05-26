<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\FindOneAndUpdate;

class FindOneAndUpdateTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), $filter, []);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], $update);
    }

    /** @dataProvider provideInvalidUpdateValues */
    public function testConstructorUpdateArgumentRequiresOperatorsOrPipeline($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an update document with operator as first key or a pipeline');
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], $update);
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

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], ['$set' => ['x' => 1]], $options);
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
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], [], ['returnDocument' => $returnDocument]);
    }

    public function provideInvalidConstructorReturnDocumentOptions()
    {
        return $this->wrapValuesForDataProvider([-1, 0, 3]);
    }
}
