<?php

namespace MongoDB\Tests\Command;

use MongoDB\Command\ListDatabases;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ListDatabasesTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListDatabases($options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'authorizedDatabases' => self::getInvalidBooleanValues(),
            'filter' => self::getInvalidDocumentValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'nameOnly' => self::getInvalidBooleanValues(),
            'session' => self::getInvalidSessionValues(),
        ]);
    }
}
