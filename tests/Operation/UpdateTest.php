<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Update;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

class UpdateTest extends TestCase
{
    #[DataProvider('provideInvalidDatabaseAndCollectionNames')]
    public function testConstructorDatabaseAndCollectionNameChecks(string $databaseName, string $collectionName): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Update($databaseName, $collectionName, ['x' => 1], ['$set' => ['x' => 1]]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException($filter instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, ['$set' => ['x' => 1]]);
    }

    #[DataProvider('provideInvalidUpdateValues')]
    public function testConstructorUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(TypeError::class);
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['y' => 1], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'arrayFilters' => self::getInvalidArrayValues(),
            'bypassDocumentValidation' => self::getInvalidBooleanValues(),
            'collation' => self::getInvalidDocumentValues(),
            'hint' => self::getInvalidHintValues(),
            'multi' => self::getInvalidBooleanValues(),
            'sort' => self::getInvalidDocumentValues(),
            'session' => self::getInvalidSessionValues(),
            'upsert' => self::getInvalidBooleanValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    #[DataProvider('provideReplacementDocuments')]
    #[DataProvider('provideEmptyUpdatePipelines')]
    public function testConstructorMultiOptionProhibitsReplacementDocumentOrEmptyPipeline($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"multi" option cannot be true unless $update has update operator(s) or non-empty pipeline');
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update, ['multi' => true]);
    }

    public function testConstructorMultiOptionProhibitsSortOption(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"sort" option cannot be used with multi-document updates');
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['$set' => ['x' => 2]], ['multi' => true, 'sort' => ['x' => 1]]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'arrayFilters' => [['x' => 1]],
            'bypassDocumentValidation' => true,
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'hint' => '_id_',
            'multi' => true,
            'upsert' => true,
            'let' => ['a' => 3],
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];
        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['$set' => ['x' => 2]], $options);

        $expected = [
            'update' => $this->getCollectionName(),
            'bypassDocumentValidation' => true,
            'updates' => [
                [
                    'q' => (object) ['x' => 1],
                    'u' => (object) ['$set' => ['x' => 2]],
                    'multi' => true,
                    'upsert' => true,
                    'arrayFilters' => [['x' => 1]],
                    'hint' => '_id_',
                    'collation' => (object) ['locale' => 'fr'],
                ],
            ],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }

    public function testExplainableCommandDocumentWithEmptyFilter(): void
    {
        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), [], ['$set' => ['x' => 2]]);

        $expected = [
            'update' => $this->getCollectionName(),
            'updates' => [
                [
                    'q' => (object) [],
                    'u' => (object) ['$set' => ['x' => 2]],
                    'multi' => false,
                    'upsert' => false,
                ],
            ],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }

    public function testExplainableCommandDocumentWithEmptyReplacement(): void
    {
        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], []);

        $expected = [
            'update' => $this->getCollectionName(),
            'updates' => [
                [
                    'q' => (object) ['x' => 1],
                    'u' => (object) [],
                    'multi' => false,
                    'upsert' => false,
                ],
            ],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }

    public function testExplainableCommandDocumentWithPipeline(): void
    {
        $pipeline = [['$set' => ['x' => 2]]];
        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $pipeline);

        $expected = [
            'update' => $this->getCollectionName(),
            'updates' => [
                [
                    'q' => (object) ['x' => 1],
                    'u' => $pipeline,
                    'multi' => false,
                    'upsert' => false,
                ],
            ],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
