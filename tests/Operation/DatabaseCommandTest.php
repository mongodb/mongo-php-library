<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DatabaseCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

class DatabaseCommandTest extends TestCase
{
    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorCommandArgumentTypeCheck($command): void
    {
        $this->expectException($command instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new DatabaseCommand($this->getDatabaseName(), $command);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DatabaseCommand($this->getDatabaseName(), ['ping' => 1], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'session' => self::getInvalidSessionValues(),
            'typeMap' => self::getInvalidArrayValues(),
        ]);
    }
}
