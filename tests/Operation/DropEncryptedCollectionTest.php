<?php

namespace MongoDB\Tests\Operation;

use Generator;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\DropEncryptedCollection;
use PHPUnit\Framework\Attributes\DataProvider;

class DropEncryptedCollectionTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DropEncryptedCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public static function provideInvalidConstructorOptions(): Generator
    {
        yield 'encryptedFields is required' => [
            [],
        ];

        yield from self::createOptionDataProvider([
            'encryptedFields' => self::getInvalidDocumentValues(),
        ]);
    }
}
