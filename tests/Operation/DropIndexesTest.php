<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\DropIndexes;

class DropIndexesTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testDropIndexShouldNotAllowEmptyIndexName()
    {
        new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), '');
    }
}
