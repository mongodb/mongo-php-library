<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DropSearchIndex;

class DropSearchIndexTest extends TestCase
{
    public function testConstructorIndexNameMustNotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropSearchIndex($this->getDatabaseName(), $this->getCollectionName(), '');
    }
}
