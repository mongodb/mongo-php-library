<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\FindAndModify;
use PHPUnit\Framework\Attributes\DataProvider;

class FindAndModifyTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'arrayFilters' => self::getInvalidArrayValues(),
            'bypassDocumentValidation' => self::getInvalidBooleanValues(),
            'codec' => self::getInvalidDocumentCodecValues(),
            'collation' => self::getInvalidDocumentValues(),
            'fields' => self::getInvalidDocumentValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'new' => self::getInvalidBooleanValues(),
            'query' => self::getInvalidDocumentValues(),
            'remove' => self::getInvalidBooleanValues(),
            'session' => self::getInvalidSessionValues(),
            'sort' => self::getInvalidDocumentValues(),
            'typeMap' => self::getInvalidArrayValues(),
            'update' => self::getInvalidUpdateValues(),
            'upsert' => self::getInvalidBooleanValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    public function testConstructorUpdateAndRemoveOptionsAreMutuallyExclusive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "remove" option must be true or an "update" document must be specified, but not both');
        new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true, 'update' => []]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'arrayFilters' => [['x' => 1]],
            'bypassDocumentValidation' => true,
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'fields' => ['_id' => 0],
            'hint' => '_id_',
            'maxTimeMS' => 100,
            'new' => true,
            'query' => ['y' => 2],
            'sort' => ['x' => 1],
            'update' => ['$set' => ['x' => 2]],
            'upsert' => true,
            'let' => ['a' => 3],
            // Intentionally omitted options
            'remove' => false, // When "update" is set
            'typeMap' => ['root' => 'array'],
            'writeConcern' => new WriteConcern(0),
        ];
        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), $options);

        $expected = [
            'findAndModify' => $this->getCollectionName(),
            'new' => true,
            'upsert' => true,
            'collation' => (object) ['locale' => 'fr'],
            'fields' => (object) ['_id' => 0],
            'let' => (object) ['a' => 3],
            'query' => (object) ['y' => 2],
            'sort' => (object) ['x' => 1],
            'update' => (object) ['$set' => ['x' => 2]],
            'arrayFilters' =>  [['x' => 1]],
            'bypassDocumentValidation' => true,
            'comment' => 'explain me',
            'hint' => '_id_',
            'maxTimeMS' => 100,
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
