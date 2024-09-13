<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\ModifyCollection;

class ModifyCollectionTest extends TestCase
{
    public function testConstructorEmptyCollectionOptions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$collectionOptions is empty');
        new ModifyCollection($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ModifyCollection($this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'session' => self::getInvalidSessionValues(),
            'typeMap' => self::getInvalidArrayValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }
}
