<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DropDatabase;
use PHPUnit\Framework\Attributes\DataProvider;

class DropDatabaseTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropDatabase($this->getDatabaseName(), $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'session' => self::getInvalidSessionValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }
}
