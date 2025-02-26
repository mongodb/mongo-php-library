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
}
