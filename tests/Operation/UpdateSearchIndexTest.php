<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\UpdateSearchIndex;
use TypeError;

class UpdateSearchIndexTest extends TestCase
{
    public function testConstructorIndexNameMustNotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UpdateSearchIndex($this->getDatabaseName(), $this->getCollectionName(), '', []);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorIndexDefinitionMustBeADocument($definition): void
    {
        $this->expectException($definition instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new UpdateSearchIndex($this->getDatabaseName(), $this->getCollectionName(), 'index name', $definition);
    }
}
