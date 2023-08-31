<?php

namespace MongoDB\Tests\Operation;

use Generator;
use MongoDB\BSON\Document;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\CreateIndexes;
use stdClass;

class CreateIndexesTest extends TestCase
{
    public function testConstructorIndexesArgumentMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$indexes is not a list');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [1 => ['key' => ['x' => 1]]]);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => ['x' => 1]]], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            // commitQuorum is int|string, for which no helper exists
            'commitQuorum' => ['float' => 3.14, 'bool' => true, 'array' => [], 'object' => new stdClass()],
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'session' => $this->getInvalidSessionValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
        ]);
    }

    public function testConstructorRequiresAtLeastOneIndex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$indexes is empty');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    /** @dataProvider provideInvalidIndexSpecificationTypes */
    public function testConstructorRequiresIndexSpecificationsToBeAnArray($index): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected $index[0] to have type "array"');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [$index]);
    }

    public function provideInvalidIndexSpecificationTypes()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidArrayValues());
    }

    public function testConstructorRequiresIndexSpecificationKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required "key" document is missing from index specification');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [[]]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorRequiresIndexSpecificationKeyToBeADocument($key): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "key" option to have type "document" (array or object)');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => $key]]);
    }

    /** @dataProvider provideKeyDocumentsWithInvalidOrder */
    public function testConstructorValidatesIndexSpecificationKeyOrder($key): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected order value for "x" field within "key" option to have type "numeric or string"');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => $key]]);
    }

    public function provideKeyDocumentsWithInvalidOrder(): Generator
    {
        $invalidOrderValues = [true, [], new stdClass(), null];

        foreach ($invalidOrderValues as $order) {
            yield [['x' => $order]];
            yield [(object) ['x' => $order]];
            yield [new BSONDocument(['x' => $order])];
            yield [Document::fromPHP(['x' => $order])];
        }
    }

    /** @dataProvider provideInvalidStringValues */
    public function testConstructorRequiresIndexSpecificationNameToBeString($name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "name" option to have type "string"');
        new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => ['x' => 1], 'name' => $name]]);
    }
}
