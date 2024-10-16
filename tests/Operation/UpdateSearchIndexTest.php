<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\UpdateSearchIndex;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

class UpdateSearchIndexTest extends TestCase
{
    public function testConstructorIndexNameMustNotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UpdateSearchIndex($this->getDatabaseName(), $this->getCollectionName(), '', []);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorIndexDefinitionMustBeADocument($definition): void
    {
        $this->expectException($definition instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new UpdateSearchIndex($this->getDatabaseName(), $this->getCollectionName(), 'index name', $definition);
    }
}
