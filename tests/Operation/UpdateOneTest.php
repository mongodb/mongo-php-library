<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\UpdateOne;

class UpdateOneTest extends TestCase
{
    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), $filter, ['$set' => ['x' => 1]]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    /**
     * @dataProvider provideUpdateDocuments
     * @doesNotPerformAssertions
     */
    public function testConstructorUpdateArgument($update): void
    {
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    /**
     * @dataProvider provideReplacementDocuments
     */
    public function testConstructorUpdateArgumentRequiresOperators($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an update document with operator as first key or a pipeline');
        new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    public function provideReplacementDocuments()
    {
        return $this->wrapValuesForDataProvider([
            ['y' => 1],
            (object) ['y' => 1],
            new BSONDocument(['y' => 1]),
        ]);
    }

    public function provideUpdateDocuments()
    {
        return $this->wrapValuesForDataProvider([
            ['$set' => ['y' => 1]],
            (object) ['$set' => ['y' => 1]],
            new BSONDocument(['$set' => ['y' => 1]]),
        ]);
    }
}
