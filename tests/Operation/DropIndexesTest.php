<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DropIndexes;

class DropIndexesTest extends TestCase
{
    public function testDropIndexShouldNotAllowEmptyIndexName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), '');
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), '*', $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'session' => $this->getInvalidSessionValues(),
            'typeMap' => $this->getInvalidArrayValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
        ]);
    }
}
