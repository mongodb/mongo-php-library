<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\FindOneAndDelete;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

class FindOneAndDeleteTest extends TestCase
{
    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException($filter instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new FindOneAndDelete($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndDelete($this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'projection' => self::getInvalidDocumentValues(),
        ]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'hint' => '_id_',
            'maxTimeMS' => 100,
            'sort' => ['x' => 1],
            'let' => ['a' => 3],
            // Intentionally omitted options
            'projection' => ['_id' => 0],
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];
        $operation = new FindOneAndDelete($this->getDatabaseName(), $this->getCollectionName(), ['y' => 2], $options);

        $expected = [
            'findAndModify' => $this->getCollectionName(),
            'collation' => (object) ['locale' => 'fr'],
            'fields' => (object) ['_id' => 0],
            'let' => (object) ['a' => 3],
            'query' => (object) ['y' => 2],
            'sort' => (object) ['x' => 1],
            'comment' => 'explain me',
            'hint' => '_id_',
            'maxTimeMS' => 100,
            'remove' => true,
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
