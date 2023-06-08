<?php

namespace MongoDB\Tests\Model;

use MongoDB\BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\SearchIndexInput;
use MongoDB\Tests\TestCase;

class SearchIndexInputTest extends TestCase
{
    public function testConstructorIndexDefinitionMustBeDefined(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required "definition" document is missing from search index specification');
        new SearchIndexInput([]);
    }

    public function testConstructorIndexDefinitionMustBeADocument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "definition" option to have type "document"');
        new SearchIndexInput(['definition' => 'foo']);
    }

    public function testConstructorShouldRequireNameToBeString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "name" option to have type "string"');
        new SearchIndexInput(['definition' => ['mapping' => ['dynamid' => true]], 'name' => 1]);
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
