<?php

namespace MongoDB\Tests\Model;

use MongoDB\BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\SearchIndexInput;
use MongoDB\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class SearchIndexInputTest extends TestCase
{
    public function testConstructorIndexDefinitionMustBeDefined(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required "definition" document is missing from search index specification');
        new SearchIndexInput([]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorIndexDefinitionMustBeADocument($definition): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "definition" option to have type "document"');
        new SearchIndexInput(['definition' => $definition]);
    }

    #[DataProvider('provideInvalidStringValues')]
    public function testConstructorShouldRequireNameToBeString($name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "name" option to have type "string"');
        new SearchIndexInput(['definition' => ['mapping' => ['dynamic' => true]], 'name' => $name]);
    }

    #[DataProvider('provideInvalidStringValues')]
    public function testConstructorShouldRequireTypeToBeString($type): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "type" option to have type "string"');
        new SearchIndexInput(['definition' => ['mapping' => ['dynamic' => true]], 'type' => $type]);
    }

    public function testBsonSerialization(): void
    {
        $expected = (object) [
            'name' => 'my_search',
            'definition' => ['mapping' => ['dynamic' => true]],
        ];

        $indexInput = new SearchIndexInput([
            'name' => 'my_search',
            'definition' => ['mapping' => ['dynamic' => true]],
        ]);

        $this->assertInstanceOf(Serializable::class, $indexInput);
        $this->assertEquals($expected, $indexInput->bsonSerialize());
    }
}
