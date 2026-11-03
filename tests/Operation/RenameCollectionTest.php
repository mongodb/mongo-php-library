<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\RenameCollection;
use PHPUnit\Framework\Attributes\DataProvider;

class RenameCollectionTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RenameCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            $this->getDatabaseName(),
            $this->getCollectionName() . '.renamed',
            $options,
        );
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'dropTarget' => self::getInvalidBooleanValues(),
            'session' => self::getInvalidSessionValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    #[DataProvider('provideInvalidRenameDatabaseAndCollectionNames')]
    public function testConstructorDatabaseAndCollectionNameChecks(string $fromDatabaseName, string $fromCollectionName, string $toDatabaseName, string $toCollectionName): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RenameCollection($fromDatabaseName, $fromCollectionName, $toDatabaseName, $toCollectionName);
    }

    public static function provideInvalidRenameDatabaseAndCollectionNames(): array
    {
        return [
            'dot in fromDatabaseName' => ['foo.bar', 'coll', 'db', 'coll'],
            'NUL byte in fromDatabaseName' => ["foo\0bar", 'coll', 'db', 'coll'],
            'NUL byte in fromCollectionName' => ['db', "foo\0bar", 'db', 'coll'],
            'dot in toDatabaseName' => ['db', 'coll', 'foo.bar', 'coll'],
            'NUL byte in toDatabaseName' => ['db', 'coll', "foo\0bar", 'coll'],
            'NUL byte in toCollectionName' => ['db', 'coll', 'db', "foo\0bar"],
        ];
    }
}
