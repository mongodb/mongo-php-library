<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DropCollection;

class DropCollectionTest extends TestCase
{
    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'session' => $this->getInvalidSessionValues(),
            'typeMap' => $this->getInvalidArrayValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
        ]);
    }
}
