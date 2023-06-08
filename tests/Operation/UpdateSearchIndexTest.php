<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\UpdateSearchIndex;

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
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected $definition to have type "document"');
        new UpdateSearchIndex($this->getDatabaseName(), $this->getCollectionName(), 'index name', $definition);
    }
}
