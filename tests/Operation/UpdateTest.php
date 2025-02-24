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
                    'q' => ['x' => 1],
                    'u' => ['$set' => ['x' => 2]],
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
}
