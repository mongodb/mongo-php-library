<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CountDocuments;
use MongoDB\Operation\InsertMany;

class CountDocumentsFunctionalTest extends FunctionalTestCase
{
    public function testEmptyCollection(): void
    {
        $operation = new CountDocuments($this->getDatabaseName(), $this->getCollectionName(), []);
        $this->assertSame(0, $operation->execute($this->getPrimaryServer()));
    }

    public function testNonEmptyCollection(): void
    {
        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 1],
            ['x' => 2],
            ['y' => 3],
            ['z' => 4],
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $operation = new CountDocuments($this->getDatabaseName(), $this->getCollectionName(), []);
        $this->assertSame(4, $operation->execute($this->getPrimaryServer()));
    }
}
