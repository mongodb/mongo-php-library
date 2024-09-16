<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\ListIndexes;

class ListIndexesTest extends TestCase
{
    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListIndexes($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'session' => self::getInvalidSessionValues(),
        ]);
    }
}
