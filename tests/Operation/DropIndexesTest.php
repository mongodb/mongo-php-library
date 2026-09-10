<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DropIndexes;
use PHPUnit\Framework\Attributes\DataProvider;

class DropIndexesTest extends TestCase
{
    public function testDropIndexShouldNotAllowEmptyIndexName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), '');
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), '*', $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'session' => self::getInvalidSessionValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }
}
