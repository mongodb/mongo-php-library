<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CreateSearchIndexes;

class CreateSearchIndexesTest extends TestCase
{
    public function testConstructorIndexesArgumentMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$indexes is not a list');
        new CreateSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), [1 => ['name' => 'index name', 'definition' => ['mappings' => ['dynamic' => true]]]], []);
    }

    /** @dataProvider provideInvalidArrayValues */
    public function testConstructorIndexDefinitionMustBeADocument($index): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected $indexes[0] to have type "array"');
        new CreateSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), [$index], []);
    }

    /** @dataProvider provideInvalidStringValues */
    public function testConstructorIndexNameMustBeAString($name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "name" option to have type "string"');
        new CreateSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), [['name' => $name, 'definition' => ['mappings' => ['dynamic' => true]]]], []);
    }

    public function testConstructorIndexDefinitionMustBeDefined(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required "definition" document is missing from search index specification');
        new CreateSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), [['name' => 'index name']], []);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorIndexDefinitionMustBeAnArray($definition): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "definition" option to have type "document"');
        new CreateSearchIndexes($this->getDatabaseName(), $this->getCollectionName(), [['definition' => $definition]], []);
    }
}
