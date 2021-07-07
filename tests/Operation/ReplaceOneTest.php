<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\ReplaceOne;

class ReplaceOneTest extends TestCase
{
    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), $filter, ['y' => 1]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
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

    /**
     * @dataProvider provideUpdateDocuments
     */
    public function testConstructorReplacementArgumentRequiresNoOperators($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $replacement argument is an update operator');
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

    public function provideUpdateDocuments()
    {
        return $this->wrapValuesForDataProvider([
            ['$set' => ['y' => 1]],
            (object) ['$set' => ['y' => 1]],
            new BSONDocument(['$set' => ['y' => 1]]),
        ]);
    }
}
