<?php

namespace MongoDB\Tests\Operation;

use Generator;
use MongoDB\BSON\Document;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\CreateIndexes;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

class CreateIndexesTest extends TestCase
{
    public function testConstructorIndexesArgumentMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$indexes is not a list');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [1 => ['key' => ['x' => 1]]]);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => ['x' => 1]]], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            // commitQuorum is int|string, for which no helper exists
            'commitQuorum' => ['float' => 3.14, 'bool' => true, 'array' => [], 'object' => new stdClass()],
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'session' => self::getInvalidSessionValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    public function testConstructorRequiresAtLeastOneIndex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$indexes is empty');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    #[DataProvider('provideInvalidIndexSpecificationTypes')]
    public function testConstructorRequiresIndexSpecificationsToBeAnArray($index): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected $index[0] to have type "array"');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [$index]);
    }

    public static function provideInvalidIndexSpecificationTypes()
    {
        return self::wrapValuesForDataProvider(self::getInvalidArrayValues());
    }

    public function testConstructorRequiresIndexSpecificationKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required "key" document is missing from index specification');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [[]]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorRequiresIndexSpecificationKeyToBeADocument($key): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "key" option to have type "document" (array or object)');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => $key]]);
    }

    #[DataProvider('provideKeyDocumentsWithInvalidOrder')]
    public function testConstructorValidatesIndexSpecificationKeyOrder($key): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected order value for "x" field within "key" option to have type "numeric or string"');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => $key]]);
    }

    public static function provideKeyDocumentsWithInvalidOrder(): Generator
    {
        $invalidOrderValues = [true, [], new stdClass(), null];

        foreach ($invalidOrderValues as $order) {
            yield [['x' => $order]];
            yield [(object) ['x' => $order]];
            yield [new BSONDocument(['x' => $order])];
            yield [Document::fromPHP(['x' => $order])];
        }
    }

    #[DataProvider('provideInvalidStringValues')]
    public function testConstructorRequiresIndexSpecificationNameToBeString($name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "name" option to have type "string"');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => ['x' => 1], 'name' => $name]]);
    }
}
